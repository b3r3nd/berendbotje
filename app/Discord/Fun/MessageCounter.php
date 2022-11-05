<?php

namespace App\Discord\Fun;

use App\Discord\Core\Bot;
use App\Models\DiscordUser;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageCounter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            $lastMessageDate = Bot::get()->getGuild($message->guild_id)->getLastMessage($message->author->id);

            $guild = Bot::get()->getGuild($message->guild_id);

            if ($lastMessageDate->diffInSeconds(Carbon::now()) >= $guild->getSetting('xp_cooldown')) {
                $user = DiscordUser::get($message->author->id);
                $guild->setLastMessage($message->author->id);

                $messageCounters = $user->messageCounters()->where('guild_id', $guild->model->id)->get();
                $messageCounter = new \App\Models\MessageCounter(['count' => 1, 'guild_id' => $guild->model->id]);

                if ($messageCounters->isEmpty()) {
                    $user->messageCounters()->save($messageCounter);
                } else {
                    $bumpCounter = $messageCounter->first();
                    $bumpCounter->update(['count' => $messageCounter->count + 1]);
                }
            }
        });
    }

}
