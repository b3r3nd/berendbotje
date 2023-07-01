<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $command = \App\Discord\Fun\Models\Command::create(['trigger' => $this->getOption('command'), 'response' => $this->getOption('response'), 'guild_id' => Guild::get($this->guildId)->id]);
        $command->save();
        return EmbedFactory::successEmbed($this, __('bot.cmd.saved', ['trigger' => $this->getOption('command'), 'response' => $this->getOption('response')]));
    }
}
