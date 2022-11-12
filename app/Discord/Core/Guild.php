<?php

namespace App\Discord\Core;

use App\Discord\Core\Enums\Setting as SettingEnum;
use App\Discord\Moderation\Command\SimpleCommand;
use App\Models\Channel;
use App\Models\Guild as GuildModel;
use App\Models\LogSetting;
use App\Models\Setting;
use Carbon\Carbon;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Embed\Embed;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Exception;

/**
 * Guild settings are loaded on boot and only updated when the actual setting is changed using commands.
 *
 * When a new command or reaction is added a new instance of either class is instantiated. I cannot manually destroy
 * these instances when the command or reaction is deleted, so I keep track of them here and make sure they do not fire.
 * @see SimpleCommand
 * @see SimpleReaction
 * @property $deletedCommands   List deleted commands so they do not trigger.
 * @property $deletedReactions  List of deleted reactions so they do not rigger.
 * @property $settings          List of caches settings, so we do not need to read from the database each time
 * @property $lastMessages      Last message send by user in guild, used for the xp cooldown.
 * @property $inVoice           List of people who are currently in voice in the guild, used to calculate xp.
 * @property $guildModel        Eloquent model for the guild.
 * @property $logger            Logger instance for this specific guild which can log events.
 *
 * @TODO find better solution for deleted commands and reactions.. probably step away from having a single instance per trigger
 */
class Guild
{
    private array $deletedCommands = [];
    private array $deletedReactions = [];
    private array $settings = [];
    private array $logSettings = [];
    private array $lastMessages = [];
    private array $inVoice = [];
    public GuildModel $model;
    private Logger $logger;
    private array $channels = [];

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
        } else {
            return 0;
        }
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
     * @param string $channel
     * @return void
     */
    public function addChannel(string $channel): void
    {
        $channel = Channel::create(['channel_id' => $channel, 'guild_id' => $this->model->id]);
        $this->channels[$channel] = $channel;
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
        if (!$channel->no_xp && !$channel->media_only) {
            unset($this->channels[$channel->channel_id]);
            $channel->delete();
        } else {
            // Becauase we "cache" the list of channels to prevent a billion DB calls, we need to refresh the model
            $this->channels[$channel->channel_id]->refresh();
        }
    }

    /**
     * @param string $channel
     * @param string $key
     * @param bool $value
     * @return void
     */
    public function setChannelValue(string $channel, string $key, bool $value): void
    {
        $channel = $this->guildModel->channels()->where('channel_id', $channel)->first();
        $channel->update([$key => $value]);
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

        if ($key == SettingEnum::LOG_CHANNEL->value) {
            $this->logger->setLogChannelId($value);
        }

        $setting = Setting::getSetting($key, $this->model->guild_id);
        $setting->value = $value;
        $setting->save();
    }

    /**
     * @param Enums\Setting $setting
     * @return false|mixed
     */
    public function getSetting(\App\Discord\Core\Enums\Setting $setting): mixed
    {
        $setting = $setting->value;

        if (str_contains($setting, 'enable')) {
            if ($this->settings[$setting] === '1') {
                return true;
            }
            return false;
        } else if (is_numeric($this->settings[$setting])) {
            return (int)$this->settings[$setting];
        }

        return $this->settings[$setting] ?? "";
    }

    /**
     * @param string $command
     * @return void
     */
    public function deleteCommand(string $command): void
    {
        $this->deletedCommands[] = strtolower($command);
    }

    /**
     * @param string $reaction
     * @return void
     */
    public function deleteReaction(string $reaction): void
    {
        $this->deletedReactions[] = strtolower($reaction);
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
    public function getMediaChannels(): array
    {
        return $this->mediaChannels ?? [];
    }

    /**
     * @return array
     */
    public function getDeletedCommands(): array
    {
        return $this->deletedCommands;
    }

    /**
     * @return array
     */
    public function getDeletedReactions(): array
    {
        return $this->deletedReactions;
    }

    /**
     * @return array
     */
    public function getLastMessages(): array
    {
        return $this->lastMessages;
    }
}
