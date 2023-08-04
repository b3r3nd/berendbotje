<?php

namespace App\Discord\Levels\Commands\RoleReward;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Fun\Models\RoleReward;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class DeleteRoleReward extends SlashCommand
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
        $this->description = __('bot.slash.del-role-reward');
        $this->slashCommandOptions = [
            [
                'name' => 'level',
                'description' => __('bot.level'),
                'type' => Option::INTEGER,
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
        RoleReward::where(['level' => $this->getOption('level'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this, __('bot.rewards.deleted', ['level' => $this->getOption('level')]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return $this->getAutoComplete(RoleReward::class, $interaction->guild_id, 'level', $this->getOption('level'));
    }
}
