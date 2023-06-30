<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        if (!Role::exists($this->guildId, $this->getOption('role_name'))) {
            return EmbedFactory::failedEmbed($this, __('bot.roles.not-exist', ['role' => $this->getOption('role_name')]));
        }

        if (strtolower($this->getOption('role_name')) === 'admin' && Guild::get($this->guildId)->owner->discord_id === $this->getOption('user_mention')) {
            return EmbedFactory::failedEmbed($this, __('bot.roles.admin-role-owner'));
        }

        $role = Role::get($this->guildId, $this->getOption('role_name'));
        $user = DiscordUser::get($this->getOption('user_mention'));
        $user->roles()->detach($role);

        return EmbedFactory::successEmbed($this, __('bot.roles.role-detached', ['role' => $role->name, 'user' => $user->tag()]));
    }
}
