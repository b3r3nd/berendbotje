<?php

namespace App\Discord\Roles;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class DeleteRole extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::DELETE_ROLE;
    }

    public function trigger(): string
    {
        return 'delrole';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delete-role');
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
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            return EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]]));
        }
        if (!Role::get($this->guildId, $this->arguments[0])->users->isEmpty()) {
            return EmbedFactory::failedEmbed(__('bot.roles.has-users'));
        }
        if (strtolower($this->arguments[0]) === 'admin') {
            return EmbedFactory::failedEmbed(__('bot.roles.admin-role'));
        }

        Role::get($this->guildId, $this->arguments[0])->delete();
        return EmbedFactory::successEmbed(__('bot.roles.deleted', ['role' => $this->arguments[0]]));
    }
}
