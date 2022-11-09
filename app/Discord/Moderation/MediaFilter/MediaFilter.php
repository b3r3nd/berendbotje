<?php

namespace App\Discord\Moderation\MediaFilter;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MediaFilter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            if (!in_array($message->channel, Bot::get()->getGuild($message->guild_id ?? "")->getMediaChannels())) {
                return;
            }

            // If message contains Images, audio or any other file we allow it
            if ($message->attachments->count() > 0) {
                return;
            }

            // If the message contains a valid URL we allow it.
            foreach (explode(' ', $message->content) as $word) {
                if (filter_var($word, FILTER_VALIDATE_URL)) {
                    return;
                }
            }


            $message->delete();
            $message->author->sendMessage(__('bot.media-deleted', ['channel' => $message->channel->name]));
        });
    }


}
