<?php

namespace App\Discord\Levels;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class GiveXp extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_XP;
    }

    public function trigger(): string
    {
        return 'givexp';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.givexp');
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

    public function action(): MessageBuilder
    {
        (new UpdateMessageCounterAction($this->guildId, $this->getOption('user_mention'), $this->getOption('user_xp'), $this->bot))->execute();
        return EmbedFactory::successEmbed($this->discord, __('bot.xp.given', ['user' => $this->getOption('user_mention'), 'xp' => $this->getOption('user_xp')]));
    }
}
