<?php

namespace App\Discord\Moderation\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class CreateCommand extends SlashCommand
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
        $this->description = __('bot.slash.add-command');
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
        $trigger = $this->arguments[0];
        $response = $this->arguments[1];
        $command = \App\Models\Command::create(['trigger' => $trigger, 'response' => $response, 'guild_id' => Guild::get($this->guildId)->id]);
        $command->save();
        new SimpleCommand(Bot::get(), $trigger, $response, $this->guildId);
        return EmbedFactory::successEmbed(__('bot.cmd.saved', ['trigger' => $trigger, 'response' => $response]));
    }
}
