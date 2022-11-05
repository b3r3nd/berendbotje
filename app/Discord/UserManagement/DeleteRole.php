<?php

namespace App\Discord\UserManagement;

use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Guild;
use App\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class DeleteRole extends SlashAndMessageCommand
{

    public function permission(): string
    {
        return 'delete role';
    }

    public function trigger(): string
    {
        return 'delrole';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->usageString = __('bot.roles.usage-delrole');
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
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            return EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]]));
        }
        Role::get($this->guildId, $this->arguments[0])->delete();
        return EmbedFactory::successEmbed(__('bot.roles.deleted', ['role' => $this->arguments[0]]));
    }
}
