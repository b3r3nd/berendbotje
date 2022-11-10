<?php

namespace App\Discord\Roles;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\Role;

class DetachRolePermission extends MessageCommand
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
        $this->requiredArguments = 2;
        $this->usageString = __('bot.roles.usage-detachperm');

        parent::__construct();
    }

    public function action(): void
    {
        (new SyncRolePermissionsAction($this->message, $this->arguments, $this->guildId, false))->execute();
    }
}
