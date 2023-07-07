<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;

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


    /**
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = Role::byDiscordGuildId($this->guildId)->count();
        $userRoles = [];
        foreach (Role::byDiscordGuildId(($this->guildId))->orderBy('created_at', 'asc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $role) {
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

        return EmbedBuilder::create($this, __('bot.users.title'), $description)->getEmbed();
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
