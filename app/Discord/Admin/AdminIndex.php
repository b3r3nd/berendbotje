<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;

class AdminIndex extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'admins';
    }

    public function action(): void
    {
        $description = "";
        foreach (Admin::orderBy('level', 'desc')->get() as $admin) {
            $description .= "** {$admin->discord_username} ** - {$admin->level} \n";
        }
        $embed = EmbedBuilder::create(Bot::get()->discord(),
            __('bot.admins.title'),
            __('bot.admins.footer'),
            __('bot.admins.description', ['admins' => $description]));

        $this->message->channel->sendEmbed($embed);
    }

}
