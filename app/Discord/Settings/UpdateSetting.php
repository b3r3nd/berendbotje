<?php

namespace App\Discord\Settings;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use App\Models\Setting;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

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

    public function action(): MessageBuilder
    {
        if (!Setting::hasSetting($this->arguments[0], $this->guildId)) {
            return EmbedFactory::failedEmbed(__('bot.set.not-exist', ['key' => $this->arguments[0]]));
        }
        if (!is_numeric($this->arguments[1])) {
            return EmbedFactory::failedEmbed(__('bot.set.not-numeric', ['value' => $this->arguments[1]]));
        }

        $this->bot->getGuild($this->guildId)?->setSetting($this->arguments[0], $this->arguments[1]);
        return EmbedFactory::successEmbed(__('bot.set.updated', ['key' => $this->arguments[0], 'value' => $this->arguments[1]]));
    }
}
