<?php

namespace App\Discord\Roles;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Interfaces\Action;
use App\Models\Permission;
use App\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;

class SyncRolePermissionsAction
{
    private array $arguments;
    private string $guildId;
    private bool $attach;

    /**
     * @param array $arguments
     * @param string $guildId
     * @param bool $attach
     */
    public function __construct(array $arguments, string $guildId, bool $attach = true)
    {
        $this->arguments = $arguments;
        $this->guildId = $guildId;
        $this->attach = $attach;
    }

    /**
     * @return MessageBuilder
     */
    public function execute(): MessageBuilder
    {
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            return EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]]));
        }
        if (!$this->processPermissions($this->arguments[1])) {
            return EmbedFactory::failedEmbed(__('bot.permissions.not-exist', ['perm' => $this->arguments[1]]));
        }
        if (strtolower($this->arguments[0]) === 'admin') {
            return EmbedFactory::failedEmbed(__('bot.roles.admin-role-perms'));
        }

        $role = Role::get($this->guildId, $this->arguments[0]);
        $permissions = $this->processPermissions($this->arguments[1]);

        if ($this->attach) {
            $role->permissions()->attach($permissions->pluck('id'));
            return EmbedFactory::successEmbed(__('bot.roles.perm-attached', ['role' => $role->name, 'perm' => $this->arguments[1]]));
        } else {
            $role->permissions()->detach($permissions->pluck('id'));
            return EmbedFactory::successEmbed(__('bot.roles.perm-detached', ['role' => $role->name, 'perm' => $this->arguments[1]]));
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
