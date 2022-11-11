<?php

namespace App\Discord\Roles;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\DiscordUser;
use App\Models\Role;
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
        if (!Role::exists($this->guildId, $this->arguments[1])) {
            return EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[1]]));
        }
        $role = Role::get($this->guildId, $this->arguments[1]);

        $user = DiscordUser::get($this->arguments[0]);
        $user->roles()->attach($role);

        return EmbedFactory::successEmbed(__('bot.roles.role-attached', ['role' => $role->name, 'user' => $user->tag()]));
    }
}
