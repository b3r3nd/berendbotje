<?php

namespace App\Discord\Fun\SimpleReaction;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class CreateReaction extends SlashAndMessageCommand
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
                'name' => 'trigger',
                'description' => 'Trigger',
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
        $command = Reaction::create(['trigger' => $this->arguments[0], 'reaction' => $this->arguments[1], 'guild_id' => $this->guildId]);
        $command->save();
        new SimpleReaction(Bot::get(), $this->arguments[0], $this->arguments[1], $this->guildId);
        return EmbedFactory::successEmbed(__('bot.reactions.saved', ['name' => $this->arguments[0], 'reaction' => $this->arguments[1]]));
    }
}
