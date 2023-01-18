<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\LogSetting;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class UpdateLogSetting extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::LOGS;
    }

    public function trigger(): string
    {
        return 'logset';
    }

    public function __construct()
    {
        $choices = [];
        foreach (LogSetting::where('guild_id', 1)->get() as $setting) {
            $choices[] = ['name' => $setting->key, 'value' => $setting->key];
        }

        $this->description = __('bot.slash.logset');
        $this->slashCommandOptions = [
            [
                'name' => 'setting_key',
                'description' => 'Key',
                'type' => Option::STRING,
                'required' => true,
                'choices' => $choices,
            ],
            [
                'name' => 'setting_value',
                'description' => 'Value',
                'type' => Option::BOOLEAN,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        Bot::get()->getGuild($this->guildId)?->setLogSetting($this->arguments[0], $this->arguments[1]);
        return EmbedFactory::successEmbed(__('bot.logset.updated', ['key' => $this->arguments[0], 'value' => $this->arguments[1]]));
    }
}
