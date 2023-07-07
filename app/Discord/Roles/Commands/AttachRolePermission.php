<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Actions\SyncRolePermissionsAction;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class AttachRolePermission extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ATTACH_PERM;
    }

    public function trigger(): string
    {
        return 'addperm';
    }

    public function __construct()
    {
        $choices = [];
        foreach (\App\Discord\Roles\Models\Permission::all() as $permission) {
            $choices[] = ['name' => __("bot.permissions-enum.{$permission->name}"), 'value' => $permission->name];
        }

        $this->description = __('bot.slash.attach-role-perm');
        $this->slashCommandOptions = [
            [
                'name' => 'role_name',
                'description' => __('bot.role'),
                'type' => Option::STRING,
                'required' => true,
                'autocomplete' => true,
            ],
            [
                'name' => 'permissions',
                'description' => __('bot.permission'),
                'type' => Option::STRING,
                'required' => true,
                'choices' => $choices,

            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        return (new SyncRolePermissionsAction($this, $this->interaction->data->options, $this->guildId))->execute();
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return $this->getAutoComplete(Role::class, $interaction->guild_id, 'name', $this->getOption('role_name'));

    }
}
