<?php

namespace App\Discord\Levels\Commands\LevelReward;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Fun\Models\RoleReward;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class CreateRoleReward extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ROLE_REWARDS;
    }

    public function trigger(): string
    {
        return 'add';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.add-role-reward');
        $this->slashCommandOptions = [
            [
                'name' => 'level',
                'description' => __('bot.level'),
                'type' => Option::INTEGER,
                'required' => true,
            ],
            [
                'name' => 'role',
                'description' => __('bot.role'),
                'type' => Option::ROLE,
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
        $roleReward = RoleReward::create(['level' => $this->getOption('level'), 'role' => $this->getOption('role'), 'guild_id' => \App\Domain\Discord\Guild::get($this->guildId)->id]);
        $roleReward->save();
        return EmbedFactory::successEmbed($this, __('bot.rewards.added', ['level' => $this->getOption('level'), 'role' => $roleReward->roleTag()]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
