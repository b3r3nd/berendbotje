<?php

namespace App\Discord\Core\Settings;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Setting;

class UpdateSetting extends MessageCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'set';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->usageString = __('bot.set.usage-set');
        parent::__construct();
    }


    public function action(): void
    {
        $setting = Setting::where(['key' => $this->arguments[0], 'guild_id' => $this->guildId])->first();

        if (!$setting) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.set.not-exist', ['key' => $this->arguments[0]])));
            return;
        }

        $setting->value = $this->arguments[1];
        $setting->save();
        Bot::get()->setSetting($this->arguments[0], $this->arguments[1], $this->guildId);
        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.set.updated', ['key' => $this->arguments[0], 'value' => $this->arguments[1]])));
    }
}
