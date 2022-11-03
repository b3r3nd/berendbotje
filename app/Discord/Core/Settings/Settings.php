<?php

namespace App\Discord\Core\Settings;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Setting;

class Settings extends MessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
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

        foreach (Setting::byGuild($this->guildId)->get() as $setting) {
            $embedBuilder->getEmbed()->addField(['name' => $setting->key, 'value' => $setting->value]);
        }

        $this->message->channel->sendEmbed($embedBuilder->getEmbed());
    }
}
