<?php

namespace App\Discord\Roles\Actions;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Permission\Models\Permission;
use App\Domain\Permission\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection;
use Exception;

class SyncRolePermissionsAction
{
    public function __construct(
        private readonly SlashCommand $command,
        private readonly Collection   $options,
        private readonly string       $guildId,
        private readonly bool         $attach = true,
    )
    {
    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function execute(): MessageBuilder
    {
        $roleOption = $this->options->first()->options->get('name', 'role_name')->value;
        $permOption = $this->options->first()->options->get('name', 'permissions')->value;

        if (!Role::exists($this->guildId, $roleOption)) {
            return EmbedFactory::failedEmbed($this->command, __('bot.roles.not-exist', ['role' => $roleOption]));
        }
        if (!$this->processPermissions($permOption)) {
            return EmbedFactory::failedEmbed($this->command, __('bot.permissions.not-exist', ['perm' => $permOption]));
        }
        if (strtolower($this->options[0]) === 'admin') {
            return EmbedFactory::failedEmbed($this->command, __('bot.roles.admin-role-perms'));
        }

        $role = Role::get($this->guildId, $roleOption);
        $permissions = $this->processPermissions($permOption);

        if ($this->attach) {
            $role->permissions()->attach($permissions->pluck('id'));
            return EmbedFactory::successEmbed($this->command, __('bot.roles.perm-attached', ['role' => $role->name, 'perm' => $permOption]));
        }

        $role->permissions()->detach($permissions->pluck('id'));
        return EmbedFactory::successEmbed($this->command, __('bot.roles.perm-detached', ['role' => $role->name, 'perm' => $permOption]));
    }

    /**
     * @param $parameters
     * @return bool|Collection
     */
    private function processPermissions($parameters): bool|\Illuminate\Support\Collection
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
            }
            $attach->push(Permission::get($permission));
        }
        return $attach;
    }
}
