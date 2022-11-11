<?php

namespace App\Discord\Roles;

use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class DetachRolePermission extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ATTACH_PERM;
    }

    public function trigger(): string
    {
        return 'delperm';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.detach-role-perm');
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
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        return (new SyncRolePermissionsAction($this->arguments, $this->guildId, false))->execute();
    }
}
