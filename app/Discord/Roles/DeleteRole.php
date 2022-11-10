<?php

namespace App\Discord\Roles;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\Role;

class DeleteRole extends MessageCommand
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
        $this->requiredArguments = 1;
        $this->usageString = __('bot.roles.usage-delrole');

        parent::__construct();
    }

    public function action(): void
    {
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]])));
            return;
        }

        if (!Role::get($this->guildId, $this->arguments[0])->users->isEmpty()) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.has-users')));
            return;
        }

        if(strtolower($this->arguments[0]) === 'admin') {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.admin-role')));
            return;
        }

        Role::get($this->guildId, $this->arguments[0])->delete();
        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.deleted', ['role' => $this->arguments[0]])));
    }
}
