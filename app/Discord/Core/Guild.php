<?php

namespace App\Discord\Core;

use App\Discord\Core\Enums\Setting as SettingEnum;
use App\Discord\Fun\QuestionOfTheDayReminder;
use App\Models\Channel;
use App\Models\DiscordUser;
use App\Models\Guild as GuildModel;
use App\Models\LogSetting;
use App\Models\MentionGroup;
use App\Models\Setting;
use App\Models\Timeout;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Discord\WebSockets\Event;
use Exception;

/**
 * Guild settings are loaded on boot and only updated when the actual setting is changed using commands.
 *
 * @property $settings          List of cached settings, so we do not need to read from the database each time.
 * @property $logSettings       List of cached log settings, so we do not need to read from the database each time.
 * @property $lastMessages      Last message send by user in guild, used for the xp cooldown.
 * @property $inVoice           List of people who are currently in voice in the guild, used to calculate xp.
 * @property $guildModel        Eloquent model for the guild.
 * @property $logger            Logger instance for this specific guild which can log events.
 * @property $channels          List of channels which have special flags set, for example media channels.
 */
class Guild
{
    private array $settings = [];
    private array $logSettings = [];
    private array $lastMessages = [];
    private array $inVoice = [];
    public GuildModel $model;
    private Logger $logger;
    private array $channels = [];
    public MentionResponder $mentionResponder;
    public QuestionOfTheDayReminder $questionOfTheDayReminder;

    /**
     * @param GuildModel $guild
     */
    public function __construct(GuildModel $guild)
    {
        $this->model = $guild;

        foreach ($this->model->settings as $setting) {
            $this->settings[$setting->key] = $setting->value;
        }

        foreach ($this->model->logSettings as $setting) {
            $this->logSettings[$setting->key] = $setting->value;
        }

        foreach ($this->model->channels as $channel) {
            $this->channels[$channel->channel_id] = $channel;
        }

        $this->logger = new Logger($this->getSetting(SettingEnum::LOG_CHANNEL));
        $this->registerReactions();
        $this->registerCommands();

        $this->mentionResponder = new MentionResponder($this->model->guild_id);
        $this->questionOfTheDayReminder = new QuestionOfTheDayReminder($this->model->guild_id);
    }

    /**
     * @return void
     */
    private function registerReactions(): void
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot || !$this->getSetting(\App\Discord\Core\Enums\Setting::ENABLE_REACTIONS)) {
                return;
            }
            $this->model->refresh();
            $msg = strtolower($message->content);

            foreach ($this->model->reactions as $reaction) {
                preg_match("/\b{$reaction->trigger}\b|^{$reaction->trigger}\b|\b{$reaction->trigger}$/", $msg, $result);

                if (!empty($result)) {
                    if (str_contains($reaction->reaction, "<")) {
                        $message->react(str_replace(["<", ">"], "", $reaction->reaction));
                    } else {
                        $message->react($reaction->reaction);
                    }
                }
            }
        });
    }

    /**
     * @return void
     */
    private function registerCommands(): void
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot || !$this->getSetting(\App\Discord\Core\Enums\Setting::ENABLE_COMMANDS)) {
                return;
            }
            $this->model->refresh();
            foreach ($this->model->commands as $command) {
                if (strtolower($message->content) === strtolower($command->trigger)) {
                    $message->channel->sendMessage($command->response);
                }
            }
        });
    }

    /**
     * @param string $message
     * @param string $type
     * @return void
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
        if (isset($this->lastMessages[$userId])) {
            return $this->lastMessages[$userId];
        }
        return Carbon::now()->subMinutes(100);
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
     * @param Enums\LogSetting $setting
     * @return false|mixed
     */
    public function getLogSetting(\App\Discord\Core\Enums\LogSetting $setting): mixed
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
