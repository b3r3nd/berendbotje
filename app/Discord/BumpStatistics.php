<?php

namespace App\Discord;

use App\Models\Bumper;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class BumpStatistics
{
    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if ((str_starts_with($message->content, $bot->getPrefix() . 'bumpstats'))) {
                foreach (Bumper::orderBy('count', 'desc')->limit(10)->get() as $bumper) {
                    $message->channel->sendMessage($bumper->discord_username . ' -> ' . $bumper->count);
                }
            }
        });
    }

}
