<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleCommand
{
    /**
     * @param Discord $discord
     * @param string $trigger
     * @param string $response
     * @return void
     */
    public static function create(Discord $discord, string $trigger, string $response)
    {
        new self($discord, $trigger, $response);
    }

    /**
     * @param Discord $discord
     * @param string $trigger
     * @param string $response
     */
    public function __construct(Discord $discord, string $trigger, string $response)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($trigger, $response) {
            if ($message->author->bot) {
                return;
            }
            if (strtolower($message->content) == strtolower($trigger)) {
                $message->reply($response);
            }
        });
    }
}
