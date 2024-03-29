<?php

namespace App\Discord\Moderation\Commands\Command;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class CreateCommand extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::COMMANDS;
    }

    public function trigger(): string
    {
        return 'add';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.add-command');
        $this->slashCommandOptions = [
            [
                'name' => 'command',
                'description' => __('bot.command'),
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'response',
                'description' => __('bot.response'),
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
        $command = \App\Domain\Fun\Models\Command::create(['trigger' => $this->getOption('command'), 'response' => $this->getOption('response'), 'guild_id' => Guild::get($this->guildId)->id]);
        $command->save();
        return EmbedFactory::successEmbed($this, __('bot.cmd.saved', ['trigger' => $this->getOption('command'), 'response' => $this->getOption('response')]));
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
