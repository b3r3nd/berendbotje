<?php

namespace App\Discord\Roles;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Permission;
use Discord\Parts\Embed\Embed;

class Permissions extends SlashAndMessageIndexCommand
{
    public function permission(): string
    {
        return 'permissions';
    }

    public function trigger(): string
    {
        return 'permissions';
    }

    public function getEmbed(): Embed
    {
        $this->perPage = 30;
        $this->total = Permission::count();
        $description = "";
        foreach (Permission::orderBy('created_at', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $permission) {
            $description .= "{$permission->name}\n";
        }

        return EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.permissions.title'))
            ->setFooter(__('bot.permissions.footer'))
            ->setDescription(__('bot.permissions.description', ['perms' => $description]))
            ->getEmbed();
    }
}
