<?php

namespace App\Discord\Levels;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\RoleReward;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class CreateRoleReward extends SlashCommand
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
        $this->description = __('bot.slash.add-role-reward');
        $this->slashCommandOptions = [
            [
                'name' => 'level',
                'description' => 'Level',
                'type' => Option::INTEGER,
                'required' => true,
            ],
            [
                'name' => 'role',
                'description' => 'Role',
                'type' => Option::ROLE,
                'required' => true,
            ],
        ];
        parent::__construct();
    }


    public function action(): MessageBuilder
    {
        $roleReward = RoleReward::create(['level' => $this->getOption('level'), 'role' => $this->getOption('role'), 'guild_id' => \App\Models\Guild::get($this->guildId)->id]);
        $roleReward->save();
        return EmbedFactory::successEmbed($this->discord, __('bot.rewards.added', ['level' => $this->getOption('level'), 'role' => $roleReward->roleTag()]));
    }
}
