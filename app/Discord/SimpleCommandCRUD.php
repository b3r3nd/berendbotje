<?php

namespace App\Discord;

use App\Models\Command;
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
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1]) || !isset($parameters[2])) {
                    $message->reply("Provide arguments noob");
                } else {
                    $message->reply("Command saved");
                    $command = Command::create(['trigger' => $parameters[1], 'response' => $parameters[2]]);
                    $command->save();
                    new SimpleCommand($discord, $parameters[1], $parameters[2]);
                }
            }

            if (str_starts_with($message->content,  $bot->getPrefix() . 'delcmd ')) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->reply("Provide arguments noob");
                } else {
                    Command::where(['trigger' => $parameters[1]])->delete();
                    $message->reply("Command deleted");
                }

            }

            if ($message->content ==  $bot->getPrefix() . 'commands') {
                $commands = '';
                foreach (Command::all() as $command) {
                    $commands .= $command->trigger . ' ';
                }
                $message->reply($commands);
            }

        });
    }
}
