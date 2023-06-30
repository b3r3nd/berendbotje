<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Roles\Models\Permission;
use Discord\Parts\Embed\Embed;
use Exception;

class Permissions extends SlashIndexCommand
{
    public function permission(): \App\Discord\Roles\Enums\Permission
    {
        return \App\Discord\Roles\Enums\Permission::PERMISSIONS;
    }

    public function trigger(): string
    {
        return 'permissions';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.permissions');
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->perPage = 30;
        $this->total = Permission::count();
        $description = "";
        foreach (Permission::orderBy('created_at', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $permission) {
            $label = __("bot.permissions-enum.{$permission->name}");
            $description .= "`{$permission->name}` - {$label}\n";
        }

        return EmbedBuilder::create($this, __('bot.permissions.title'), __('bot.permissions.description', ['perms' => $description]))->getEmbed();
    }
}
