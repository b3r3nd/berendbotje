<?php

namespace App\Discord\Settings;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Setting;
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
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.set.title'))
            ->setFooter(__('bot.set.footer'));

        $description = "";
        foreach (Setting::byDiscordGuildId($this->guildId)->get() as $setting) {
            if ($setting->key === \App\Discord\Core\Enums\Setting::LOG_CHANNEL->value) {
                $description .= "**{$setting->key}** = <#{$setting->value}>\n";
            }
            elseif ($setting->key === \App\Discord\Core\Enums\Setting::BUMP_REMINDER_ROLE->value) {
                $description .= "**{$setting->key}** = <@&{$setting->value}>\n";

            } else {
                $description .= "**{$setting->key}** = {$setting->value}\n";
            }
        }
        $embedBuilder->setDescription($description);

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
