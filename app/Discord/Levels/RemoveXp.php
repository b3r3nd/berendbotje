<?php

namespace App\Discord\Levels;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

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

    public function action(): MessageBuilder
    {
        (new UpdateMessageCounterAction($this->guildId, $this->arguments[0], $this->arguments[1], $this->bot, true))->execute();
        return EmbedFactory::successEmbed(__('bot.xp.removed', ['user' => $this->arguments[0], 'xp' => $this->arguments[1]]));
    }
}
