<?php

namespace App\Discord\Roles;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\DiscordUser;
use App\Models\Role;

class AttachUserRole extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::ATTACH_ROLE;
    }

    public function trigger(): string
    {
        return 'adduser';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->requiresMention = true;
        $this->usageString = __('bot.roles.usage-attachrole');
        parent::__construct();
    }

    public function action(): void
    {
        if (!Role::exists($this->guildId, $this->arguments[1])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[1]])));
            return;
        }
        $role = Role::get($this->guildId, $this->arguments[1]);

        $user = DiscordUser::get($this->arguments[0]);
        $user->roles()->attach($role);

        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.role-attached', ['role' => $role->name, 'user' => $user->tag()])));
    }
}
