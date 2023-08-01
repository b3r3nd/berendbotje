<?php

namespace App\Discord\Core\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

/**
 * The Message Create event is a bit special because it is used a lot throughout the bot. I don't like
 * adding a bunch of listeners in separate classes since it will both cause duplicate code and will execute
 * the same functions multiple times on each message (->getGuild() for example).
 *
 * This listener is simply a kind of wrapper which calls the actual Event listeners on the MESSAGE_CREATE event.
 */
class MessageCreate extends DiscordEvent
{
    public function event(): string
    {
        return Event::MESSAGE_CREATE;
    }

    /**
     * @param Message $message
     * @param Discord $discord
     * @return void
     */
    public function execute(Message $message, Discord $discord): void
    {
        if ($message->author->bot || !$message->guild_id) {
            return;
        }
        $guild = $this->bot->getGuild($message->guild_id);
        if (!$guild) {
            return;
        }

        // @TODO remove when all new usernames are updated
        DiscordUser::get($message->member);

        $channel = $guild->getChannel($message->channel_id);
        foreach ($this->bot->messageActions as $messageEvent) {
            $messageEvent->execute($this->bot, $guild, $message, $channel ?: null);
        }
    }
}
