<?php

namespace App\Discord\UserManagement;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Role;
use Discord\Parts\Embed\Embed;

class Users extends SlashAndMessageIndexCommand
{

    public function permission(): string
    {
        return "";
    }

    public function trigger(): string
    {
        return 'users';
    }


    public function getEmbed(): Embed
    {
        $this->total = Role::byDiscordGuildId($this->guildId)->count();

        $description = "";
        foreach (Role::byDiscordGuildId(($this->guildId))->orderBy('created_at', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $role) {
            $users = "";
            foreach ($role->users as $user) {
                $users .= "{$user->tag()}\n ";
            }
            $description .= "** {$role->name} **\n $users\n\n";
        }
        return EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.roles.title'))
            ->setFooter(__('bot.roles.footer'))
            ->setDescription(__('bot.roles.description', ['roles' => $description]))
            ->getEmbed();
    }
}
