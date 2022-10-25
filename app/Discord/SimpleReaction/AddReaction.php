<?php

namespace App\Discord\SimpleReaction;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class AddReaction extends SlashCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'addreaction';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->usageString = __('bot.reactions.usage-addreaction');
        $this->slashCommandOptions = [
            [
                'name' => 'command',
                'description' => 'Command',
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'reaction',
                'description' => 'Reaction',
                'type' => Option::STRING,
                'required' => true,
            ]
        ];
        parent::__construct();
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {
        $command = Reaction::create(['trigger' => $this->arguments[0], 'reaction' => $this->arguments[1]]);
        $command->save();
        new SimpleReaction(Bot::get(), $this->arguments[0], $this->arguments[1]);
        return EmbedFactory::successEmbed(__('bot.reactions.saved', ['name' => $this->arguments[0], 'reaction' => $this->arguments[1]]));
    }
}
