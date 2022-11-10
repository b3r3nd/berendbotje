<?php

namespace App\Discord\Settings;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Models\Setting;

class UpdateSetting extends MessageCommand
{
    public function permission(): Permission
    {
        return Permission::CONFIG;
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
        if (!Setting::hasSetting($this->arguments[0], $this->guildId)) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.set.not-exist', ['key' => $this->arguments[0]])));
            return;
        }
        Bot::get()->getGuild($this->guildId)->setSetting($this->arguments[0], $this->arguments[1]);
        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.set.updated', ['key' => $this->arguments[0], 'value' => $this->arguments[1]])));
    }
}
