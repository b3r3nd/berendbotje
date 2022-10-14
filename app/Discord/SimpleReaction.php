<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleReaction
{
    public static function create(Discord $discord, string $trigger, string $reaction)
    {
        new self($discord, $trigger, $reaction);
    }

    /**
     * @param Discord $discord
     * @param string $trigger
     * @param string $reaction
     */
    public function __construct(Discord $discord, string $trigger, string $reaction)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($trigger, $reaction) {
            if ($message->author->bot) {
                return;
            }
            if (str_contains(strtolower($message->content), strtolower($trigger))) {
                $message->react($reaction);
            }
        });
    }
}
