<?php

namespace App\Discord\Roles;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Permission;
use App\Models\Role;

class DetachRolePermission extends MessageCommand
{

    public function permission(): string
    {
        return 'attach-permission';
    }

    public function trigger(): string
    {
        return 'unsetperm';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->usageString = __('bot.roles.usage-detachperm');

        parent::__construct();
    }

    public function action(): void
    {
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]])));
            return;
        }
        if (!Permission::exists($this->arguments[1])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.permissions.not-exist', ['perm' => $this->arguments[1]])));
            return;
        }

        $role = Role::get($this->guildId, $this->arguments[0]);
        $permission = Permission::get($this->arguments[1]);
        $role->permissions()->detach($permission);

        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.perm-detached', ['role' => $role->name, 'perm' => $permission->name])));
    }
}
