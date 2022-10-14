<?php

namespace App\Discord;

use App\Models\Bumper;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class Test
{

    public function __construct(Discord $discord)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->type == 20 && $message->interaction->name == 'alert') {
                $bumper = Bumper::where(['discord_id' => $message->interaction->user->id])->first();

                if ($bumper) {
                    $bumper->count = $bumper->count + 1;
                    $bumper->save();
                } else {
                    $bumper = Bumper::create(['discord_id' => $message->interaction->user->id, 'count' => 1]);
                }

                $message->channel->sendMessage($message->interaction->user . ' heeft ' . $bumper->count . ' x de discord gebumped!');

            }
        });
    }
}
