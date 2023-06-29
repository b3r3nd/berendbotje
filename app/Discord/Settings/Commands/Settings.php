<?php

namespace App\Discord\Settings\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Settings\Models\Setting;
use Discord\Builders\MessageBuilder;

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

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create($this->discord)
            ->setTitle(__('bot.set.title'))
            ->setFooter(__('bot.set.footer'));

        $description = "";
        foreach (Setting::byDiscordGuildId($this->guildId)->get() as $setting) {
            if ($setting->key === \App\Discord\Settings\Enums\Setting::LOG_CHANNEL->value) {
                $description .= "**{$setting->key}** = <#{$setting->value}>\n";
            } elseif ($setting->key === \App\Discord\Settings\Enums\Setting::BUMP_CHANNEL->value
                || $setting->key === \App\Discord\Settings\Enums\Setting::REMINDER_CHANNEL->value) {
                $description .= "**{$setting->key}** = <#{$setting->value}>\n";
            } elseif ($setting->key === \App\Discord\Settings\Enums\Setting::BUMP_REMINDER_ROLE->value
                || $setting->key === \App\Discord\Settings\Enums\Setting::REMINDER_ROLE->value) {
                $description .= "**{$setting->key}** = <@&{$setting->value}>\n";

            } else {
                $description .= "**{$setting->key}** = {$setting->value}\n";
            }
        }
        $embedBuilder->setDescription($description);

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
