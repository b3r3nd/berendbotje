<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageLogger
{

    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_UPDATE, function (Message $message, Discord $discord, ?Message $oldMessage) {
            if ($message->author->bot) {
                return;
            }
            $guild = Bot::get()->getGuild($message->guild_id);

            if (isset($oldMessage)) {
                $desc = "Updated message in <#{$message->channel_id}>

                **Old Message**
                {$oldMessage->content}

                **New Message**
                {$message->content}
                ";
                $guild->logWithMember($message->member, $desc, 'warning');

            }
        });


        Bot::getDiscord()->on(Event::MESSAGE_DELETE, function (object $message, Discord $discord) {
            if ($message instanceof Message) {
                if ($message->author->bot) {
                    return;
                }

                $guild = Bot::get()->getGuild($message->guild_id);
                $desc = "Message deleted in <#{$message->channel_id}>

                **Message**
                {$message->content}";
                $guild->logWithMember($message->member, $desc, 'fail');
            }
        });
    }
}