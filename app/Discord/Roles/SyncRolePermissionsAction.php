<?php

namespace App\Discord\Roles;

use App\Discord\Core\Action;
use App\Discord\Core\EmbedFactory;
use App\Models\Permission;
use App\Models\Role;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;

class SyncRolePermissionsAction implements Action
{
    private Message $message;
    private array $arguments;
    private string $guildId;
    private bool $attach;

    /**
     * @param Message $message
     * @param array $arguments
     * @param string $guildId
     * @param bool $attach
     */
    public function __construct(Message $message, array $arguments, string $guildId, bool $attach = true)
    {
        $this->message = $message;
        $this->arguments = $arguments;
        $this->guildId = $guildId;
        $this->attach = $attach;
    }

    /**
     * @return void
     * @throws NoPermissionsException
     */
    public function execute(): void
    {
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]])));
            return;
        }
        if (!$this->processPermissions($this->arguments[1])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.permissions.not-exist', ['perm' => $this->arguments[1]])));
            return;
        }
        if (strtolower($this->arguments[0]) === 'admin') {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.admin-role-perms')));
            return;
        }

        $role = Role::get($this->guildId, $this->arguments[0]);
        $permissions = $this->processPermissions($this->arguments[1]);

        if ($this->attach) {
            $role->permissions()->attach($permissions->pluck('id'));
            $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.perm-attached', ['role' => $role->name, 'perm' => $this->arguments[1]])));
        } else {
            $role->permissions()->detach($permissions->pluck('id'));
            $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.perm-detached', ['role' => $role->name, 'perm' => $this->arguments[1]])));
        }
    }

    /**
     * @param $parameters
     * @return bool|Collection
     */
    private function processPermissions($parameters): bool|Collection
    {
        $parameters = strtolower($parameters);
        if (str_contains($parameters, ',')) {
            $permissions = explode(',', $parameters);
        } else {
            $permissions[] = $parameters;
        }

        $attach = collect([]);
        foreach ($permissions as $permission) {
            if (!Permission::exists($permission)) {
                return false;
            } else {
                $attach->push(Permission::get($permission));
            }
        }
        return $attach;
    }
}
