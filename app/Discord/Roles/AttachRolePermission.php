<?php

namespace App\Discord\Roles;

use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;

class AttachRolePermission extends MessageCommand
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
        $this->requiredArguments = 2;
        $this->usageString = __('bot.roles.usage-attachperm');
        parent::__construct();
    }

    public function action(): void
    {
        (new SyncRolePermissionsAction($this->message, $this->arguments, $this->guildId))->execute();
    }
}
