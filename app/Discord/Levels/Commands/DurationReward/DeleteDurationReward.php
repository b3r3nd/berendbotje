<?php

namespace App\Discord\Levels\Commands\DurationReward;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Moderation\Models\RoleReward;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class DeleteDurationReward extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ROLE_REWARDS;
    }

    public function trigger(): string
    {
        return 'delete';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.del-duration-reward');
        $this->slashCommandOptions = [
            [
                'name' => 'duration',
                'description' => __('bot.duration'),
                'type' => Option::STRING,
                'required' => true,
                'autocomplete' => 'true',
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
        RoleReward::where(['duration' => $this->getOption('duration'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this, __('bot.duration-reward.deleted', ['duration' => $this->getOption('duration')]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return $this->getAutoComplete(RoleReward::class, $interaction->guild_id, 'duration', $this->getOption('duration'));
    }
}
