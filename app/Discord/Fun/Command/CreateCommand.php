<?php

namespace App\Discord\Fun\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class CreateCommand extends SlashAndMessageCommand
{
    public function permission(): Permission
    {
        return Permission::COMMANDS;
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
        $command = \App\Models\Command::create(['trigger' => $trigger, 'response' => $response, 'guild_id' => Guild::get($this->guildId)->id]);
        $command->save();
        new SimpleCommand(Bot::get(), $trigger, $response, $this->guildId);
        return EmbedFactory::successEmbed(__('bot.cmd.saved', ['trigger' => $trigger, 'response' => $response]));
    }
}
