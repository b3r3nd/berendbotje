<?php

namespace App\Discord\Roles;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Models\Role;
use Discord\Parts\Embed\Embed;

class Users extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'users';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.users');
        parent::__construct();
    }


    public function getEmbed(): Embed
    {
        $this->total = Role::byDiscordGuildId($this->guildId)->count();
        $userRoles = [];
        foreach (Role::byDiscordGuildId(($this->guildId))->orderBy('created_at', 'asc')->skip($this->offset)->limit($this->perPage)->get() as $role) {
            foreach ($role->users as $user) {
                $userRoles[$user->tag()][] = $role->name;
            }
        }

        $description = "";
        foreach ($userRoles as $user => $roles) {
            $description .= "\n**{$user}** • ";
            foreach ($roles as $role) {
                $description .= "{$role} ";
            }
        }

        return EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.roles.title'))
            ->setFooter(__('bot.roles.footer'))
            ->setDescription(__('bot.roles.description', ['roles' => $description]))
            ->getEmbed();
    }
}
