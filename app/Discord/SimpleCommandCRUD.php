<?php

namespace App\Discord;

use App\Models\Command;
use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleCommandCRUD
{
    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'addcmd ')) {
                if (!Admin::isAdmin($message->author->id)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1]) || !isset($parameters[2])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    $message->channel->sendMessage("Command saved");
                    $command = Command::create(['trigger' => $parameters[1], 'response' => $parameters[2]]);
                    $command->save();
                    new SimpleCommand($discord, $parameters[1], $parameters[2]);
                }
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'delcmd ')) {
                if (!Admin::isAdmin($message->author->id)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    Command::where(['trigger' => $parameters[1]])->delete();
                    $message->channel->sendMessage("Command deleted");
                }

            }

            if ($message->content == $bot->getPrefix() . 'commands') {
                if (!Admin::isAdmin($message->author->id)) {
                    return;
                }
                $commands = '';
                foreach (Command::all() as $command) {
                    $commands .= $command->trigger . ' ';
                }
                $message->channel->sendMessage($commands);
            }

        });
    }
}
