<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class AttachUserRole extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ATTACH_ROLE;
    }

    public function trigger(): string
    {
        return 'adduser';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.attach-user-role');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => true,
            ],
            [
                'name' => 'role_name',
                'description' => 'Role',
                'type' => Option::STRING,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        if (!Role::exists($this->guildId, $this->getOption('role_name'))) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.roles.not-exist', ['role' => $this->getOption('role_name')]));
        }
        $role = Role::get($this->guildId, $this->getOption('role_name'));

        $user = DiscordUser::get($this->getOption('user_mention'));
        $user->roles()->attach($role);

        return EmbedFactory::successEmbed($this->discord, __('bot.roles.role-attached', ['role' => $role->name, 'user' => $user->tag()]));
    }
}
