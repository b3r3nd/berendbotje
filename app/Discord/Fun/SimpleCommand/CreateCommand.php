<?php

namespace App\Discord\Fun\SimpleCommand;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class CreateCommand extends SlashAndMessageCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'addcmd';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->usageString = __('bot.cmd.usage-addcmd');
        $this->slashCommandOptions = [
            [
                'name' => 'command',
                'description' => 'Command',
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'response',
                'description' => 'Response',
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
        $trigger = array_shift($this->arguments);
        $response = join(' ', $this->arguments);
        $command = \App\Models\Command::create(['trigger' => $trigger, 'response' => $response, 'guild_id' => $this->guildId]);
        $command->save();
        new SimpleCommand(Bot::get(), $trigger, $response, $this->guildId);
        return EmbedFactory::successEmbed(__('bot.cmd.saved', ['trigger' => $trigger, 'response' => $response]));
    }
}
