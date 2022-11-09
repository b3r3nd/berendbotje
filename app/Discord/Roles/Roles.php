<?php

namespace App\Discord\Roles;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\Permission;
use App\Models\Role;
use Discord\Parts\Embed\Embed;

class Roles extends SlashAndMessageIndexCommand
{
    public function permission(): Permission
    {
        return Permission::ROLES;
    }

    public function trigger(): string
    {
        return 'roles';
    }

    public function getEmbed(): Embed
    {
        $this->total = Role::byDiscordGuildId($this->guildId)->count();
        $description = "";
        foreach (Role::byDiscordGuildId(($this->guildId))->orderBy('created_at', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $role) {
            $perms = "";
            foreach ($role->permissions as $permission) {
                $perms .= "{$permission->name}, ";
            }
            $description .= "** {$role->name} **\n $perms\n\n";
        }
        return EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.roles.title'))
            ->setFooter(__('bot.roles.footer'))
            ->setDescription(__('bot.roles.description', ['roles' => $description]))
            ->getEmbed();
    }
}
