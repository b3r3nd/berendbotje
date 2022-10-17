<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;
use App\Models\Command;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleCommandCRUD
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if (!Admin::hasLevel($message->author->id, AccessLevels::MOD->value)) {
                return;
            }
            if (str_starts_with($message->content, "{$bot->getPrefix()}addcmd")) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1]) || !isset($parameters[2])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
                } else {
                    $message->channel->sendMessage(__('bot.cmd.saved'));
                    $command = Command::create(['trigger' => $parameters[1], 'response' => $parameters[2]]);
                    $command->save();
                    new SimpleCommand($bot, $parameters[1], $parameters[2]);
                }
            }
            if (str_starts_with($message->content,"{$bot->getPrefix()}delcmd")) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
                } else {
                    Command::where(['trigger' => $parameters[1]])->delete();
                    $bot->deleteCommand($parameters[1]);
                    $message->channel->sendMessage(__('bot.cmd.deleted'));
                }
            }
            if ($message->content == "{$bot->getPrefix()}commands") {
                $commands = "";
                foreach (Command::all() as $command) {
                    $commands .= "** {$command->trigger} ** - {$command->response}\n";
                }
                $embed = EmbedBuilder::create($discord,
                    __('bot.cmd.title'),
                    __('bot.cmd.footer'),
                    __('bot.cmd.description', ['cmds' => $commands]));
                $message->channel->sendEmbed($embed);
            }
        });
    }
}
