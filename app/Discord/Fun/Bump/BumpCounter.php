<?php

namespace App\Discord\Fun\Bump;

use App\Discord\Core\Bot;
use App\Models\Bumper;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;


class BumpCounter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->type == 20 && $message->interaction->name == 'bump') {
                $user = DiscordUser::getByGuild($message->interaction->user->id, $message->guild_id);

                if ($user->bumper) {
                    $user->bumper()->update(['count' => $user->bumper->count + 1]);
                } else {
                    $user->bumper()->save(new Bumper(['count' => 1]));
                }

                $user->refresh();
                $message->channel->sendMessage(__('bot.bump.inc', ['name' => $message->interaction->user->username, 'count' => $user->bumper->count ?? 0]));
            }
        });
    }
}
