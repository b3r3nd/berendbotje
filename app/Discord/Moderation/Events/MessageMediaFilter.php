<?php

namespace App\Discord\Moderation\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Moderation\Models\Channel;
use Discord\Parts\Channel\Message;

class MessageMediaFilter implements MessageCreateAction
{
    public function execute(Bot $bot, Guild $guildModel, Message $message, ?Channel $channel): void
    {
        if (!$channel || !$channel->media_only || $message->attachments->count() > 0) {
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
    }
}
