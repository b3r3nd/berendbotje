<?php

namespace App\Discord\Moderation\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Models\Channel;
use Discord\Parts\Channel\Message;

class StickerFilter implements MessageCreateAction
{
    public function execute(Bot $bot, Guild $guild, Message $message, ?Channel $channel): void
    {
        if (!$channel || !$channel->no_stickers) {
            return;
        }
        // If message contains stickers we delete the message
        if ($message->sticker_items?->count() > 0) {
            $message->delete();
        }
    }
}
