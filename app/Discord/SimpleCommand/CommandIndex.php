<?php

namespace App\Discord\SimpleCommand;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;

class CommandIndex extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'commands';
    }

    public function action(): void
    {
        $commands = "";
        foreach (\App\Models\Command::all() as $command) {
            $commands .= "** {$command->trigger} ** - {$command->response}\n";
        }

        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.cmd.title'))
            ->setFooter(__('bot.cmd.footer'))
            ->setDescription(__('bot.cmd.description', ['cmds' => $commands]));

        $this->message->channel->sendEmbed($embedBuilder->getEmbed());
    }
}
