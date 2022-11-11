<?php

namespace App\Discord\Roles;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\DiscordUser;
use App\Models\Guild;
use App\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class DetachUserRole extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ATTACH_ROLE;
    }

    public function trigger(): string
    {
        return 'deluser';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.detach-user-role');
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

        if (strtolower($this->arguments[1]) === 'admin' && Guild::get($this->guildId)->owner->discord_id === $this->arguments[0]) {
            return EmbedFactory::failedEmbed(__('bot.roles.admin-role-owner'));
        }

        $role = Role::get($this->guildId, $this->arguments[1]);
        $user = DiscordUser::get($this->arguments[0]);
        $user->roles()->detach($role);

        return EmbedFactory::successEmbed(__('bot.roles.role-detached', ['role' => $role->name, 'user' => $user->tag()]));
    }
}
