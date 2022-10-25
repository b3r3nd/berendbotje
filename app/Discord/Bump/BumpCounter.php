<?php

namespace App\Discord\Bump;

use App\Discord\Core\Bot;
use App\Models\Bumper;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

/**
 * @TODO Use Command class. Currently the command class does not support a message type and interaction name check.
 */
class BumpCounter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->type == 20 && $message->interaction->name == 'bump') {
                $user = DiscordUser::firstOrCreate([
                    'discord_id' => $message->interaction->user->id,
                    'discord_tag' => $message->interaction->user,
                ]);

                if (!$user->has('bumper')->get()->isEmpty()) {
                    $user->bumper()->update(['count' => $user->bumper->count + 1]);
                } else {
                    $user->bumper()->save(new Bumper(['count' => 1]));
                }

                $message->channel->sendMessage(__('bot.bump.inc', ['name' => $message->interaction->user->username, 'count' => $user->bumper->count]));
            }
        });
    }
}
