<?php

namespace App\Discord\CustomCommands\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;

class CommandIndex extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::COMMANDS;
    }

    public function trigger(): string
    {
        return 'commands';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.commands');
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->total = \App\Discord\CustomCommands\Models\Command::byGuild($this->guildId)->count();
        $commands = "";
        foreach (\App\Discord\CustomCommands\Models\Command::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $command) {
            $commands .= "** {$command->trigger} ** - {$command->response}\n";
        }
        return EmbedBuilder::create($this->discord)
            ->setTitle(__('bot.cmd.title'))
            ->setFooter(__('bot.cmd.footer'))
            ->setDescription(__('bot.cmd.description', ['cmds' => $commands]))
            ->getEmbed();
    }
}
