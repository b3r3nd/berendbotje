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
        return 'list';
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
        $this->perPage = 5;
        $this->total = Role::byDiscordGuildId($this->guildId)->count();

        $roles = Role::byDiscordGuildId(($this->guildId))->orderBy('created_at', 'asc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get();

        $embedBuilder = EmbedBuilder::create($this, __('bot.users.title'));

        foreach ($roles as $role) {
            $users = "";
            foreach ($role->users as $user) {
                $users .= " {$user->tag()} ";
            }
            $embedBuilder->getEmbed()->addField(['name' => $role->name, 'value' => $users]);
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
