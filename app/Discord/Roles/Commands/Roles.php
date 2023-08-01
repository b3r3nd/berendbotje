<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;

class Roles extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::ROLES;
    }

    public function trigger(): string
    {
        return 'list';
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
        $this->perPage = 5;
        $this->total = Role::byDiscordGuildId($this->guildId)->count();
        $embedBuilder = EmbedBuilder::create($this, __('bot.roles.title'));

        foreach (Role::byDiscordGuildId(($this->guildId))->orderBy('created_at', 'asc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $role) {
            $perms = "";
            $last = $role->permissions->last();
            foreach ($role->permissions as $permission) {
                $tmp = __("bot.permissions-enum.{$permission->name}");
                $perms .= " `{$tmp}`";
                if($last !== $permission) {
                    $perms .= ",";
                }
            }
            $embedBuilder->getEmbed()->addField(['name' => $role->name, 'value' => $perms]);
        }
        return $embedBuilder->getEmbed();
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
