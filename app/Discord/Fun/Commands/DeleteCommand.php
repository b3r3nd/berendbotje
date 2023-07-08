<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Fun\Models\Command;
use App\Discord\Fun\Models\Reaction;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class DeleteCommand extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::COMMANDS;
    }

    public function trigger(): string
    {
        return 'delete';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.del-command');
        $this->slashCommandOptions = [
            [
                'name' => 'command',
                'description' => __('bot.command'),
                'type' => Option::STRING,
                'required' => true,
                'autocomplete' => true,
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
        Command::where(['trigger' => $this->getOption('command'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this, __('bot.cmd.deleted', ['trigger' => $this->getOption('command')]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return $this->getAutoComplete(Command::class, $interaction->guild_id, 'trigger', $this->getOption('command'));
    }
}
