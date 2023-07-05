<?php

namespace App\Discord\Logger\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Logger\Models\LogSetting;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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
        foreach (LogSetting::where('guild_id', 2)->get() as $setting) {
            $choices[] = ['name' => $setting->key, 'value' => $setting->key];
        }

        $this->description = __('bot.slash.logset');
        $this->slashCommandOptions = [
            [
                'name' => 'setting_key',
                'description' => __('bot.key'),
                'type' => Option::STRING,
                'required' => true,
                'choices' => $choices,
            ],
            [
                'name' => 'setting_value',
                'description' => __('bot.value'),
                'type' => Option::BOOLEAN,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $this->bot->getGuild($this->guildId)?->setLogSetting($this->getOption('setting_key'), $this->getOption('setting_value'));
        return EmbedFactory::successEmbed($this, __('bot.logset.updated', ['key' => $this->getOption('setting_key'), 'value' => $this->getOption('setting_value')]));
    }
}
