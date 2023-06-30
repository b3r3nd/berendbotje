<?php

namespace App\Discord\Settings\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Settings\Models\Setting;
use Discord\Builders\MessageBuilder;
use App\Discord\Settings\Enums\Setting as SettingEnum;
use Exception;

class Settings extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::CONFIG;
    }

    public function trigger(): string
    {
        return 'config';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.config');
        parent::__construct();
    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create($this, __('bot.set.title'));

        $channels = [
            SettingEnum::LOG_CHANNEL->value,
            SettingEnum::BUMP_CHANNEL->value,
            SettingEnum::REMINDER_CHANNEL->value,
            SettingEnum::COUNT_CHANNEL->value,
        ];

        $roles = [
            SettingEnum::BUMP_REMINDER_ROLE->value,
            SettingEnum::REMINDER_ROLE->value,
        ];


        $description = "";
        foreach (Setting::byDiscordGuildId($this->guildId)->get() as $setting) {
            if (in_array($setting->key, $channels, true)) {
                $description .= "**{$setting->key}** = <#{$setting->value}>\n";
            } elseif (in_array($setting->key, $roles, true)) {
                $description .= "**{$setting->key}** = <@&{$setting->value}>\n";
            } else {
                $description .= "**{$setting->key}** = {$setting->value}\n";
            }
        }
        $embedBuilder->setDescription($description);

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
