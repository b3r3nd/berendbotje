<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Models\Admin;
use Discord\Builders\MessageBuilder;

class AdminIndex extends SlashCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'admins';
    }

    public function action(): MessageBuilder
    {
        $description = "";
        foreach (Admin::orderBy('level', 'desc')->get() as $admin) {
            $description .= "** {$admin->user->discord_tag} ** •  {$admin->level} \n";
        }
        $embedBuilder = EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.admins.title'))
            ->setFooter(__('bot.admins.footer'))
            ->setDescription(__('bot.admins.description', ['admins' => $description]));

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }

}
