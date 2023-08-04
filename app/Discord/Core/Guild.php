<?php

namespace App\Discord\Core;

use App\Discord\Logger\Logger;
use App\Discord\MentionResponder\MentionResponder;
use App\Domain\Discord\Channel;
use App\Domain\Discord\Guild as GuildModel;
use App\Domain\Setting\Enums\Setting as SettingEnum;
use App\Domain\Setting\Models\LogSetting;
use App\Domain\Setting\Models\Setting;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Exception;

/**
 * Guild settings are loaded on boot and only updated when the actual setting is changed using commands.
 *
 * @property Discord $discord                   Set with the global discord instance from DiscordPHP.
 * @property Bot $bot                           Easy reference to the bot this guild runs in.
 * @property array $settings                    List of cached settings, so we do not need to read from the database each time.
 * @property array $logSettings                 List of cached log settings, so we do not need to read from the database each time.
 * @property array $lastMessages                Last message send by user in guild, used for the xp cooldown.
 * @property array $inVoice                     List of people who are currently in voice in the guild, used to calculate xp.
 * @property Logger $logger                     Logger instance for this specific guild which can log events.
 * @property array $channels                    List of channels which have special flags set, for example media channels.
 * @property GuildModel $model                  Eloquent model for the guild.
 * @property MentionResponder $mentionResponder MentionResponder for this guild.
 */
class Guild
{
    protected Discord $discord;
    protected Bot $bot;
    private array $settings = [];
    private array $logSettings = [];
    private array $lastMessages = [];
    private array $inVoice = [];
    private Logger $logger;
    private array $channels = [];
    public GuildModel $model;
    public MentionResponder $mentionResponder;

    /**
     * @param GuildModel $guild
     * @param Bot $bot
     * @throws Exception
     */
    public function __construct(GuildModel $guild, Bot $bot)
    {
        $this->model = $guild;
        $this->discord = $bot->discord;
        $this->bot = $bot;

        foreach ($this->model->settings as $setting) {
            $this->settings[$setting->key] = $setting->value;
        }

        foreach ($this->model->logSettings as $setting) {
            $this->logSettings[$setting->key] = $setting->value;
        }

        foreach ($this->model->channels as $channel) {
            $this->channels[$channel->channel_id] = $channel;
        }

        $this->logger = new Logger($this->getSetting(SettingEnum::LOG_CHANNEL), $this->discord);
        $this->mentionResponder = new MentionResponder($this->model->guild_id, $this->bot);
    }

    /**
     * @param string $message
     * @param string $type
     * @return void
     * @throws Exception
     */
    public function log(string $message, string $type = 'log'): void
    {
        if ($this->getSetting(SettingEnum::ENABLE_LOGGING)) {
            $this->logger->log($message, $type);
        }
    }

    /**
     * @param Member|User $member
     * @param string $description
     * @param string $type
     * @return void
     * @throws Exception
     */
    public function logWithMember(Member|User $member, string $description, string $type = 'log'): void
    {
        if ($this->getSetting(SettingEnum::ENABLE_LOGGING)) {
            $this->logger->logWithMember($member, $description, $type);
        }
    }

    /**
     * @param string $userId
     * @return void
     */
    public function joinedVoice(string $userId): void
    {
        $this->inVoice[$userId] = Carbon::now();
    }


    /**
     * @param string $userId
     * @return bool
     */
    public function isInVoice(string $userId): bool
    {
        return isset($this->inVoice[$userId]);
    }


    /**
     * @param string $userId
     * @return int
     */
    public function leftVoice(string $userId): int
    {
        if (isset($this->inVoice[$userId])) {
            $joinedAt = $this->inVoice[$userId];
            unset($this->inVoice[$userId]);
            return $joinedAt->diffInSeconds(Carbon::now());
        }

        return 0;
    }

    /**
     * @param string $channel
     * @return false|mixed
     */
    public function getChannel(string $channel): mixed
    {
        return $this->channels[$channel] ?? false;
    }

    /**
     * @param Channel $channel
     * @return void
     */
    public function updateChannel(Channel $channel): void
    {
        // If the channel does not exist it means it got its first flag applied
        if (!isset($this->channels[$channel->channel_id])) {
            $this->channels[$channel->channel_id] = $channel;
        }

        // If all flags are disabled we can remove the channel entirely
        if (!$channel->no_xp && !$channel->media_only && !$channel->no_stickers && !$channel->no_log) {
            unset($this->channels[$channel->channel_id]);
            $channel->delete();
        } else {
            // Becauase we "cache" the list of channels to prevent a billion DB calls, we need to refresh the model
            $this->channels[$channel->channel_id]->refresh();
        }
    }

    /**
     * @param string $userId
     * @return Carbon
     */
    public function getLastMessage(string $userId): Carbon
    {
        return $this->lastMessages[$userId] ?? Carbon::now()->subMinutes(100);
    }

    /**
     * @param string $userId
     * @return void
     */
    public function setLastMessage(string $userId): void
    {
        $this->lastMessages[$userId] = Carbon::now();
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function setLogSetting(string $key, $value): void
    {
        $this->logSettings[$key] = $value;
        $setting = LogSetting::getSetting($key, $this->model->guild_id);
        $setting->value = $value;
        $setting->save();
    }

    /**
     * @param \App\Domain\Setting\Enums\LogSetting $setting
     * @return false|mixed
     */
    public function getLogSetting(\App\Domain\Setting\Enums\LogSetting $setting): mixed
    {
        return $this->logSettings[$setting->value] ?? false;
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function setSetting(string $key, $value): void
    {
        $this->settings[$key] = $value;

        if ($key === SettingEnum::LOG_CHANNEL->value) {
            $this->logger->setLogChannelId($value);
        }

        $setting = Setting::getSetting($key, $this->model->guild_id);
        $setting->value = $value;
        $setting->save();
    }

    /**
     * @param SettingEnum $settingEnum
     * @return false|mixed
     */
    public function getSetting(SettingEnum $settingEnum): mixed
    {
        $setting = $settingEnum->value;

        if (str_contains($setting, 'enable')) {
            return $this->settings[$setting] === '1';
        }

        if (is_numeric($this->settings[$setting])) {
            return (int)$this->settings[$setting];
        }

        return $this->settings[$setting] ?? "";
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings ?? [];
    }

    /**
     * @return array
     */
    public function getLastMessages(): array
    {
        return $this->lastMessages;
    }
}
