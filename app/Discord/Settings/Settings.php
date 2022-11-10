<?php

namespace App\Discord\Settings;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Models\Setting;

class Settings extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::CONFIG;
    }

    public function trigger(): string
    {
        return 'config';
    }

    public function action(): void
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.set.title'))
            ->setFooter(__('bot.set.footer'));

        $description = "";
        foreach (Setting::byDiscordGuildId($this->guildId)->get() as $setting) {
            if ($setting->key === \App\Discord\Core\Enums\Setting::LOG_CHANNEL->value) {
                $description .= "**{$setting->key}** = <#{$setting->value}>\n";
            } else {
                $description .= "**{$setting->key}** = {$setting->value}\n";
            }
        }

        $embedBuilder->setDescription($description);
        $this->message->channel->sendEmbed($embedBuilder->getEmbed());
    }
}
