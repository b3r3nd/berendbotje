<?php

namespace App\Discord\Roles;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\DiscordUser;
use App\Models\Guild;
use App\Models\Role;

class DetachUserRole extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::ATTACH_ROLE;
    }

    public function trigger(): string
    {
        return 'deluser';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->requiresMention = true;
        $this->usageString = __('bot.roles.usage-detachrole');

        parent::__construct();
    }

    public function action(): void
    {
        if (!Role::exists($this->guildId, $this->arguments[1])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.not-exist', ['role' => $this->arguments[1]])));
            return;
        }

        if (strtolower($this->arguments[1]) === 'admin' && Guild::get($this->guildId)->owner->discord_id === $this->arguments[0]) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.roles.admin-role-owner')));
            return;
        }


        $role = Role::get($this->guildId, $this->arguments[1]);

        $user = DiscordUser::get($this->arguments[0]);
        $user->roles()->detach($role);

        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.roles.role-detached', ['role' => $role->name, 'user' => $user->tag()])));
    }
}
