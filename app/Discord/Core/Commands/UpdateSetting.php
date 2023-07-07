<?php

namespace App\Discord\Core\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\Models\Setting;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
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
        $this->description = __('bot.slash.set');
        $this->slashCommandOptions = [
            [
                'name' => 'setting_key',
                'description' => __('bot.key'),
                'type' => Option::STRING,
                'required' => true,
                'autocomplete' => true,
            ],
            [
                'name' => 'setting_value',
                'description' => __('bot.value'),
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

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        $choices = [];
        $value = $interaction->data->options->get('name', 'setting_key')?->value;

        $settings = Setting::where('key', 'LIKE', "%{$value}%")->limit(25)->get();
        foreach ($settings as $setting) {
            $choices[] = ['name' => $setting->key, 'value' => $setting->key];
        }

        return $choices;
    }
}
