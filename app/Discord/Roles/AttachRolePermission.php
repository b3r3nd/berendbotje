<?php

namespace App\Discord\Roles;

use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Setting;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

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
        foreach (\App\Models\Permission::all() as $permission) {
            $choices[] = ['name' => __("bot.permissions-enum.{$permission->name}"), 'value' => $permission->name];
        }

        $this->description = __('bot.slash.attach-role-perm');
        $this->slashCommandOptions = [
            [
                'name' => 'role_name',
                'description' => 'Role',
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'permissions',
                'description' => 'Permissions',
                'type' => Option::STRING,
                'required' => true,
                'choices' => $choices,

            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        return (new SyncRolePermissionsAction($this->interaction->data->options, $this->discord, $this->guildId))->execute();
    }
}
