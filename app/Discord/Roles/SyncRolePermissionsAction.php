<?php

namespace App\Discord\Roles;

use App\Discord\Core\Action;
use App\Discord\Core\EmbedFactory;
use App\Models\Role;
use Discord\Parts\Channel\Message;

class SyncRolePermissionsAction implements Action
{
    private Message $message;
    private array $arguments;
    private string $guildId;
    private bool $attach;

    public function __construct(Message $message, array $arguments, string $guildId, $attach = true)
    {
        $this->message = $message;
        $this->arguments = $arguments;
        $this->guildId = $guildId;
        $this->attach = $attach;
    }


    public function execute(): void
    {
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]])));
            return;
        }
        if (!RoleHelper::processPermissions($this->arguments[1])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.permissions.not-exist', ['perm' => $this->arguments[1]])));
            return;
        }
        if (strtolower($this->arguments[0]) === 'admin') {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.admin-role-perms')));
            return;
        }

        $role = Role::get($this->guildId, $this->arguments[0]);
        $permissions = RoleHelper::processPermissions($this->arguments[1]);

        if ($this->attach) {
            $role->permissions()->attach($permissions->pluck('id'));
            $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.perm-attached', ['role' => $role->name, 'perm' => $this->arguments[1]])));
        } else {
            $role->permissions()->detach($permissions->pluck('id'));
            $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.perm-detached', ['role' => $role->name, 'perm' => $this->arguments[1]])));
        }
    }
}
