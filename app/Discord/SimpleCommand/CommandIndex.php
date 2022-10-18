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
        $embed = EmbedBuilder::create(Bot::getDiscord(),
            __('bot.cmd.title'),
            __('bot.cmd.footer'),
            __('bot.cmd.description', ['cmds' => $commands]));
        $this->message->channel->sendEmbed($embed);
    }
}
