<?php

namespace App\Discord\Roles;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use App\Models\Role;

class AttachUserRole extends MessageCommand
{

    public function permission(): string
    {
        return 'attach-role';
    }

    public function trigger(): string
    {
        return 'setrole';
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
        if (!Role::exists($this->guildId, $this->arguments[0])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[0]])));
            return;
        }
        $role = Role::get($this->guildId, $this->arguments[0]);

        $user = DiscordUser::get($this->arguments[1]);
        $user->roles()->attach($role);

        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.role-attached', ['role' => $role->name, 'user' => $user->tag()])));
    }
}
