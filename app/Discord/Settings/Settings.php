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

        foreach (Setting::byDiscordGuildId($this->guildId)->get() as $setting) {
            $embedBuilder->getEmbed()->addField(['name' => $setting->key, 'value' => $setting->value]);
        }
        $this->message->channel->sendEmbed($embedBuilder->getEmbed());
    }
}
