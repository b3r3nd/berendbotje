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
                $desc = "<@{$message->member->id}> updates his message in <#{$message->channel_id}>

            **Old Message**
            {$oldMessage->content}

            **New Message**
            {$message->content}
            ";

                $guild->log($desc, "Message updated", 'warning');
            }
        });


        Bot::getDiscord()->on(Event::MESSAGE_DELETE, function (object $message, Discord $discord) {
            if ($message instanceof Message) {
                if ($message->author->bot) {
                    return;
                }
                $guild = Bot::get()->getGuild($message->guild_id);
                $desc = "<@{$message->member->id}> deletes his message in <#{$message->channel_id}>

                **Message**
                {$message->content}";

                $guild->log($desc, "Message Deleted", 'fail');
            }
        });
    }
}
