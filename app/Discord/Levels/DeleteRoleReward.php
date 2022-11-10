<?php

namespace App\Discord\Levels;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Models\Guild;
use App\Models\RoleReward;

class DeleteRoleReward extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::ROLE_REWARDS;
    }

    public function trigger(): string
    {
        return 'delreward';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->usageString = __('bot.rewards.usage-delreward');
        parent::__construct();
    }

    public function action(): void
    {
        RoleReward::where(['level' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id])->delete();
        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.rewards.deleted', ['level' => $this->arguments[0]])));
    }
}
