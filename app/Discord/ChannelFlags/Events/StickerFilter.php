<?php

namespace App\Discord\ChannelFlags\Events;

use App\Discord\Core\DiscordEvent;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class StickerFilter extends DiscordEvent
{
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            if (!$message->guild_id) {
                return;
            }
            $guild = $this->bot->getGuild($message->guild_id ?? "");
            if ($guild) {
                $channel = $guild->getChannel($message->channel_id);
                if (!$channel) {
                    return;
                }
                if (!$channel->no_stickers) {
                    return;
                }
            }

            // If message contains stickers we delete the message
            if ($message->sticker_items) {
                $message->delete();
            }
        });
    }
}
