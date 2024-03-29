<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Levels\Actions\UpdateMessageCounterAction;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class GiveXp extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_XP;
    }

    public function trigger(): string
    {
        return 'give';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.give-xp');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => __('bot.user-mention'),
                'type' => Option::USER,
                'required' => true,
            ],
            [
                'name' => 'user_xp',
                'description' => 'XP',
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
        (new UpdateMessageCounterAction($this->guildId, $this->getOption('user_mention'), $this->getOption('user_xp'), $this->bot))->execute();
        return EmbedFactory::successEmbed($this, __('bot.xp.given', ['user' => $this->getOption('user_mention'), 'xp' => $this->getOption('user_xp')]));
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
