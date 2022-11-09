<?php

namespace App\Discord\Roles;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\Guild;
use App\Models\Role;

class CreateRole extends MessageCommand
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
        $this->requiredArguments = 1;
        $this->usageString = __('bot.roles.usage-addrole');

        parent::__construct();
    }

    public function action(): void
    {
        if (Role::exists($this->guildId, $this->arguments[0])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.exist')));
            return;
        }

        $role = Role::create([
            'name' => $this->arguments[0],
            'guild_id' => Guild::get($this->guildId)->id,
        ]);

        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.created', ['role' => $role->name])));
    }
}
