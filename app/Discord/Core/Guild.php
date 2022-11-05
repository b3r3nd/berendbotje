<?php

namespace App\Discord\Core;

use App\Models\Setting;
use Carbon\Carbon;
use \App\Models\Guild as GuildModel;

class Guild
{
    private array $mediaChannels = [];
    private array $deletedCommands = [];
    private array $deletedReactions = [];
    private array $settings = [];
    private array $lastMessages = [];
    private GuildModel $model;

    public function __construct(GuildModel $guild)
    {
        $this->model = $guild;

        foreach ($this->model->settings as $setting) {
            $this->settings[$setting->key] = $setting->value;
        }

        foreach ($this->model->mediaChannels as $channel) {
            $this->mediaChannels[$channel->channel] = $channel->channel;
        }

    }

    /**
     * @param string $channel
     * @return void
     */
    public function addMediaChannel(string $channel): void
    {
        $this->mediaChannels[$channel] = $channel;
    }

    /**
     * @param string $channel
     * @return void
     */
    public function delMediaChannel(string $channel): void
    {
        unset($this->mediaChannels[$channel]);
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
    public function setSetting(string $key, $value): void
    {
        $this->settings[$key] = $value;

        $setting = Setting::getSetting($key, $this->model->guild_id);


        $setting->value = $value;
        $setting->save();
    }

    /**
     * @param string $setting
     * @return false|mixed
     */
    public function getSetting(string $setting): mixed
    {
        if (isset($this->settings[$setting])) {
            return $this->settings[$setting];
        }
        return false;
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