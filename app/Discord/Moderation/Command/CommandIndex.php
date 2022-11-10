<?php

namespace App\Discord\Moderation\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashAndMessageIndexCommand;
use Discord\Parts\Embed\Embed;

class CommandIndex extends SlashAndMessageIndexCommand
{
    public function permission(): Permission
    {
        return Permission::COMMANDS;
    }

    public function trigger(): string
    {
        return 'commands';
    }

    public function getEmbed(): Embed
    {
        $this->total = \App\Models\Command::byGuild($this->guildId)->count();
        $commands = "";
        foreach (\App\Models\Command::byGuild($this->guildId)->skip($this->offset)->limit($this->perPage)->get() as $command) {
            $commands .= "** {$command->trigger} ** - {$command->response}\n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.cmd.title'))
            ->setFooter(__('bot.cmd.footer'))
            ->setDescription(__('bot.cmd.description', ['cmds' => $commands]))
            ->getEmbed();
    }
}
