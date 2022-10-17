<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleReaction
{
    public static function create(Bot $bot, string $trigger, string $reaction)
    {
        new self($bot, $trigger, $reaction);
    }

    public function __construct(Bot $bot, string $trigger, string $reaction)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($trigger, $reaction) {
            if ($message->author->bot) {
                return;
            }
            if (str_contains(strtolower($message->content), strtolower($trigger))) {
                $message->react($reaction);
            }
        });
    }
}
