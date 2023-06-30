<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Levels\Models\RoleReward;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

class DeleteRoleReward extends SlashCommand
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
        $this->description = __('bot.slash.del-role-reward');
        $this->slashCommandOptions = [
            [
                'name' => 'level',
                'description' => 'Level',
                'type' => Option::INTEGER,
                'required' => true,
            ],
        ];
        parent::__construct();
    }


    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        RoleReward::where(['level' => $this->getOption('level'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this, __('bot.rewards.deleted', ['level' => $this->getOption('level')]));
    }
}
