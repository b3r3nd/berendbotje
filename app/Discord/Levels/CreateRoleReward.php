<?php

namespace App\Discord\Levels;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Models\RoleReward;

class CreateRoleReward extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::ROLE_REWARDS;
    }

    public function trigger(): string
    {
        return 'addreward';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->usageString = __('bot.rewards.usage-addreward');
        parent::__construct();
    }

    public function action(): void
    {
        if(!is_numeric($this->arguments[0]) || !is_numeric($this->arguments[1])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.rewards.number')));
            return;
        }

        $roleReward = RoleReward::create(['level' => $this->arguments[0], 'role' => $this->arguments[1], 'guild_id' => \App\Models\Guild::get($this->guildId)->id]);
        $roleReward->save();
        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.rewards.added', ['level' => $this->arguments[0], 'role' => $roleReward->roleTag()])));
    }
}