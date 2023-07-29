<?php

namespace App\Discord\Core\Events;

use App\Discord\Core\DiscordEvent;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageCreate extends DiscordEvent
{
    /**
     * @return void
     */
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot || !$message->guild_id) {
                return;
            }
            $guild = $this->bot->getGuild($message->guild_id);
            if (!$guild) {
                return;
            }
            $channel = $guild->getChannel($message->channel_id);

            foreach ($this->bot->messageEvents as $messageEvent) {
                (new $messageEvent())->execute($this->bot, $guild, $message, $channel);
            }
        });
    }
}
