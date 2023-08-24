<?php

namespace App\Discord\Setting\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Permission\Enums\Permission;
use App\Domain\Setting\Models\Setting;
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
        return 'edit';
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

        if ($key === \App\Domain\Setting\Enums\Setting::XP_COOLDOWN->value && $value < 21) {
            return EmbedFactory::failedEmbed($this, __('bot.set.min-cooldown'));
        }

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
        return $this->getAutoComplete(Setting::class, $interaction->guild_id, 'key', $this->getOption('setting_key'));
    }
}
