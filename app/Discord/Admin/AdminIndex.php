<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;
use Discord\Parts\Embed\Embed;

class AdminIndex extends SlashAndMessageIndexCommand
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
        $this->total = Admin::byGuild($this->guildId)->count();
        $description = "";
        foreach (Admin::byGuild($this->guildId)->orderBy('level', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $admin) {
            $description .= "** {$admin->user->tag()} ** • {$admin->level} \n";
        }
        return EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.admins.title'))
            ->setFooter(__('bot.admins.footer'))
            ->setDescription(__('bot.admins.description', ['admins' => $description]))
            ->getEmbed();
    }
}
