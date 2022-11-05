<?php

namespace App\Discord\UserManagement;

use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Guild;
use App\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class CreateRole extends SlashAndMessageCommand
{

    public function permission(): string
    {
        return 'create role';
    }

    public function trigger(): string
    {
        return 'addrole';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->usageString = __('bot.roles.usage-addrole');
        $this->slashCommandOptions = [
            [
                'name' => 'role_name',
                'description' => 'Rolename',
                'type' => Option::STRING,
                'required' => true,
            ]
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
