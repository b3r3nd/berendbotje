<?php

namespace App\Discord;

use App\Discord\Core\Bot;
use App\Models\Bumper;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class BumpCounter
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->type == 20 && $message->interaction->name == 'bump') {
                $bumper = Bumper::where(['discord_id' => $message->interaction->user->id])->first();
                if ($bumper) {
                    $bumper->count = $bumper->count + 1;
                    $bumper->discord_username = $message->interaction->user->username;
                    $bumper->save();
                } else {
                    $bumper = Bumper::create([
                        'discord_id' => $message->interaction->user->id,
                        'discord_tag' => $message->interaction->user,
                        'discord_username' => $message->interaction->user->username,
                        'count' => 1
                    ]);
                }
                $message->channel->sendMessage(__('bot.bump.inc', ['name' => $message->interaction->user->username, 'count' => $bumper->count]));
            }
        });
    }
}
