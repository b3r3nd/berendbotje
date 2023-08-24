<?php

namespace App\Discord\Levels\Commands\DurationReward;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Levels\Helpers\Helper;
use App\Domain\Fun\Models\RoleReward;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class CreateDurationReward extends SlashCommand
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
        $this->description = __('bot.slash.add-duration-reward');
        $this->slashCommandOptions = [
            [
                'name' => 'duration',
                'description' => __('bot.duration'),
                'type' => Option::STRING,
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
        $matches = Helper::match($this->getOption('duration'));

        if (!isset($matches['year']) && !isset($matches['month']) && !isset($matches['day'])) {
            return EmbedFactory::failedEmbed($this, __('bot.duration-reward.invalid'));
        }

        $roleReward = RoleReward::create(['duration' => $this->getOption('duration'), 'role' => $this->getOption('role'), 'guild_id' => \App\Domain\Discord\Guild::get($this->guildId)->id]);
        $roleReward->save();

        return EmbedFactory::successEmbed($this, __('bot.duration-reward.added', ['duration' => $this->getOption('duration'), 'role' => $roleReward->roleTag()]));
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
