<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;
use App\Models\Setting;

class Servers extends MessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::BOT_OWNER;
    }

    public function trigger(): string
    {
        return 'servers';
    }

    public function action(): void
    {
        $description = "";
        foreach (Setting::all()->pluck('guild_id')->unique() as $guild_id) {
            $admin = Admin::byGuild($guild_id)->orderBy('level', 'desc')->first();
            $description .= "{$guild_id} • {$admin->user->tag()}\n";
        }

        $this->message->channel->sendEmbed(EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.server.title'))
            ->setFooter(__('bot.server.footer'))
            ->setDescription(__('bot.server.description', ['servers' => $description]))
            ->getEmbed());
    }
}
