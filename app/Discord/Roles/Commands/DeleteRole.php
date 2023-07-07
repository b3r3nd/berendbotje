<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class DeleteRole extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::DELETE_ROLE;
    }

    public function trigger(): string
    {
        return 'delrole';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delete-role');
        $this->slashCommandOptions = [
            [
                'name' => 'role_name',
                'description' => __('bot.role'),
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
        if (!Role::get($this->guildId, $this->getOption('role_name'))->users->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.roles.has-users'));
        }
        if (strtolower($this->getOption('role_name')) === 'admin') {
            return EmbedFactory::failedEmbed($this, __('bot.roles.admin-role'));
        }
        $role = Role::get($this->guildId, $this->getOption('role_name'));
        $role->permissions()->detach();
        $role->delete();
        return EmbedFactory::successEmbed($this, __('bot.roles.deleted', ['role' => $this->getOption('role_name')]));
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
