<?php

namespace App\Discord\SimpleReaction;

use App\Discord\Core\Bot;
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
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($trigger, $reaction, $bot) {
            if ($message->author->bot) {
                return;
            }
            if (str_contains(strtolower($message->content), strtolower($trigger))) {
                if (!in_array($trigger, $bot->getDeletedReactions())) {
                    $message->react($reaction);
                }
            }
        });
    }
}
