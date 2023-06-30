<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Roles\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        if (Role::exists($this->guildId, $this->getOption('role_name'))) {
            return EmbedFactory::failedEmbed($this, __('bot.roles.exist'));
        }

        $role = Role::create([
            'name' => $this->getOption('role_name'),
            'guild_id' => Guild::get($this->guildId)->id,
        ]);

        return EmbedFactory::successEmbed($this, __('bot.roles.created', ['role' => $role->name]));
    }
}
