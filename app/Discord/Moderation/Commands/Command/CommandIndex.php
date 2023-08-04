<?php

namespace App\Discord\Moderation\Commands\Command;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;

class CommandIndex extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::COMMANDS;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.commands');
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = \App\Domain\Fun\Models\Command::byGuild($this->guildId)->count();
        $commands = "";
        foreach (\App\Domain\Fun\Models\Command::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $command) {
            $commands .= "** {$command->trigger} ** - {$command->response}\n";
        }
        return EmbedBuilder::create($this, __('bot.cmd.title'), __('bot.cmd.description', ['cmds' => $commands]))->getEmbed();
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
