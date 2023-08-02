<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\MESSAGE_CREATE;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Exception;

class DMLogger extends DiscordEvent implements MESSAGE_CREATE
{
    public function event(): string
    {
       return Event::MESSAGE_CREATE;
    }

    /**
     * @param Message $message
     * @param Discord $discord
     * @return void
     * @throws Exception
     */
    public function execute(Message $message, Discord $discord): void
    {
        if ($message->author?->bot) {
            return;
        }
        if ($message->guild_id) {
            return;
        }
        // Hardcoded main guild I use to test the bot
        $guild = $this->bot->getGuild("590941503917129743");
        $guild->logWithMember($message->author, __('bot.log.send-dm', ['content' => $message->content]), 'success');
    }
}
