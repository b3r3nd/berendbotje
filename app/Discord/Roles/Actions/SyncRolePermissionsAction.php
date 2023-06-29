<?php

namespace App\Discord\Roles\Actions;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Roles\Models\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Repository\Interaction\OptionRepository;
use Illuminate\Support\Collection;

class SyncRolePermissionsAction
{
    private Discord $discord;
    private OptionRepository $options;
    private string $guildId;
    private bool $attach;

    /**
     * @param OptionRepository $options
     * @param Discord $discord
     * @param string $guildId
     * @param bool $attach
     */
    public function __construct(OptionRepository $options, Discord $discord, string $guildId, bool $attach = true)
    {
        $this->options = $options;
        $this->guildId = $guildId;
        $this->attach = $attach;
        $this->discord = $discord;
    }

    /**
     * @return MessageBuilder
     */
    public function execute(): MessageBuilder
    {
        $roleOption = $this->options->get('name', 'role_name')->value;
        $permOption = $this->options->get('name', 'permissions')->value;

        if (!Role::exists($this->guildId, $roleOption)) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.roles.not-exist', ['role' => $roleOption]));
        }
        if (!$this->processPermissions($permOption)) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.permissions.not-exist', ['perm' => $permOption]));
        }
        if (strtolower($this->options[0]) === 'admin') {
             return EmbedFactory::failedEmbed($this->discord, __('bot.roles.admin-role-perms'));
        }

        $role = Role::get($this->guildId, $roleOption);
        $permissions = $this->processPermissions($permOption);

        if ($this->attach) {
            $role->permissions()->attach($permissions->pluck('id'));
            return EmbedFactory::successEmbed($this->discord, __('bot.roles.perm-attached', ['role' => $role->name, 'perm' => $permOption]));
        }

        $role->permissions()->detach($permissions->pluck('id'));
        return EmbedFactory::successEmbed($this->discord, __('bot.roles.perm-detached', ['role' => $role->name, 'perm' => $permOption]));
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
            }
            $attach->push(Permission::get($permission));
        }
        return $attach;
    }
}