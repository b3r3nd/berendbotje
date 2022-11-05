<?php

namespace App\Discord\Fun\Bump;

use App\Discord\Core\Bot;
use App\Models\Bumper;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use App\Models\Guild;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;


class BumpCounter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->type == 20 && $message->interaction->name == 'bump') {
                $user = DiscordUser::get($message->interaction->user->id);
                $guild = Guild::get($message->guild_id);

                $bumpCounters = $user->bumpCounters()->where('guild_id', $guild->id)->get();

                $bumpCounter = new Bumper(['count' => 1, 'guild_id' => $guild->id]);

                if ($bumpCounters->isEmpty()) {
                    $user->bumpCounters()->save($bumpCounter);
                } else {
                    $bumpCounter = $bumpCounters->first();
                    $bumpCounter->update(['count' => $bumpCounter->count + 1]);
                }

                $user->refresh();
                $message->channel->sendMessage(__('bot.bump.inc', ['name' => $message->interaction->user->username, 'count' => $bumpCounter->count ?? 0]));
            }
        });
    }
}
