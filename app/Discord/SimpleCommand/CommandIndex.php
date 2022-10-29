<?php

namespace App\Discord\SimpleCommand;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\IndexCommand;
use App\Discord\Core\EmbedBuilder;
use Discord\Parts\Embed\Embed;

class CommandIndex extends IndexCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'commands';
    }

    public function getEmbed(): Embed
    {
        $this->total = \App\Models\Command::count();
        $commands = "";
        foreach (\App\Models\Command::skip($this->offset)->limit($this->perPage)->get() as $command) {
            $commands .= "** {$command->trigger} ** - {$command->response}\n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.cmd.title'))
            ->setFooter(__('bot.cmd.footer'))
            ->setDescription(__('bot.cmd.description', ['cmds' => $commands]))
            ->getEmbed();
    }
}
