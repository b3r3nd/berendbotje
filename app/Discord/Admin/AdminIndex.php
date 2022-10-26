<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Discord\Core\SlashIndexCommand;
use App\Models\Admin;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Embed\Embed;

class AdminIndex extends SlashIndexCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'admins';
    }

    public function getEmbed(): Embed
    {
        $this->total = Admin::count();
        $description = "";
        foreach (Admin::orderBy('level', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $admin) {
            $description .= "** {$admin->user->discord_tag} ** •  {$admin->level} \n";
        }
        return EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.admins.title'))
            ->setFooter(__('bot.admins.footer'))
            ->setDescription(__('bot.admins.description', ['admins' => $description]))
            ->getEmbed();
    }
}
