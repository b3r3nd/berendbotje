<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Parts\Embed\Embed;
use Exception;

class Roles extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::ROLES;
    }

    public function trigger(): string
    {
        return 'roles';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.roles');
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = Role::byDiscordGuildId($this->guildId)->count();
        $description = "";
        foreach (Role::byDiscordGuildId(($this->guildId))->orderBy('created_at', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $role) {
            $perms = "";
            foreach ($role->permissions as $permission) {
                $perms .= "{$permission->name}, ";
            }
            $description .= "** {$role->name} **\n $perms\n\n";
        }
        return EmbedBuilder::create($this, __('bot.roles.title'), $description)->getEmbed();
    }
}
