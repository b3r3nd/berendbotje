<?php

namespace App\Discord\SimpleCommand;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleCommand
{

    public static function create(Bot $bot, string $trigger, string $response)
    {
        new self($bot, $trigger, $response);
    }

    public function __construct(Bot $bot, string $trigger, string $response)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($trigger, $response, $bot) {
            if ($message->author->bot) {
                return;
            }
            if (strtolower($message->content) == strtolower($trigger)) {
                if (!in_array(strtolower($message->content), $bot->getDeletedCommands())) {
                    $message->channel->sendMessage($response);
                }
            }
        });
    }
}
