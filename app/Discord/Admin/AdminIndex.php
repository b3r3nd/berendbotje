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
            $description .= "** {$admin->user->discord_tag} ** •  {$admin->level} \n";
        }
        $embedBuilder = EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.admins.title'))
            ->setFooter(__('bot.admins.footer'))
            ->setDescription(__('bot.admins.description', ['admins' => $description]));

        $this->message->channel->sendEmbed($embedBuilder->getEmbed());
    }

}
