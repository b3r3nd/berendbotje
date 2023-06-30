<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Levels\Actions\UpdateMessageCounterAction;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

class RemoveXp extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_XP;
    }

    public function trigger(): string
    {
        return 'removexp';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.remove-xp');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => true,
            ],
            [
                'name' => 'user_xp',
                'description' => 'xp',
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
        (new UpdateMessageCounterAction($this->guildId, $this->getOption('user_mention'), $this->getOption('user_xp'), $this->bot, true))->execute();
        return EmbedFactory::successEmbed($this, __('bot.xp.removed', ['user' => $this->getOption('user_mention'), 'xp' => $this->getOption('user_xp')]));
    }
}
