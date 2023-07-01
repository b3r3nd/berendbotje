<?php

namespace App\Discord\Core\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Setting;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

class UpdateSetting extends SlashCommand
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
        $choices = [];
        foreach (Setting::where('guild_id', 2)->get() as $setting) {
            $choices[] = ['name' => $setting->key, 'value' => $setting->key];
        }

        $this->description = __('bot.slash.set');
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
                'type' => Option::STRING,
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
        $key = $this->getOption('setting_key');
        $value = $this->getOption('setting_value');

        if (!Setting::hasSetting($key, $this->guildId)) {
            return EmbedFactory::failedEmbed($this, __('bot.set.not-exist', ['key' => $key]));
        }
        if (!is_numeric($value)) {
            return EmbedFactory::failedEmbed($this, __('bot.set.not-numeric', ['value' => $value]));
        }

        $this->bot->getGuild($this->guildId)?->setSetting($key, $value);
        return EmbedFactory::successEmbed($this, __('bot.set.updated', ['key' => $key, 'value' => $value]));
    }
}
