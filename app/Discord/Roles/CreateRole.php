<?php

namespace App\Discord\Roles;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use App\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class CreateRole extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::CREATE_ROLE;
    }

    public function trigger(): string
    {
        return 'addrole';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.create-role');
        $this->slashCommandOptions = [
            [
                'name' => 'role_name',
                'description' => 'Role',
                'type' => Option::STRING,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        if (Role::exists($this->guildId, $this->arguments[0])) {
            return EmbedFactory::failedEmbed(__('bot.roles.exist'));
        }

        $role = Role::create([
            'name' => $this->arguments[0],
            'guild_id' => Guild::get($this->guildId)->id,
        ]);

        return EmbedFactory::successEmbed(__('bot.roles.created', ['role' => $role->name]));
    }
}
