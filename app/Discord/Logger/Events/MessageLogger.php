<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageLogger
{

    public function __construct()
    {

        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author?->bot) {
                return;
            }
            if ($message->guild_id) {
                return;
            }
            
            $guild = Bot::get()->getGuild("590941503917129743");
            $guild->logWithMember($message->author, "Send DM:\n\n" . $message->content, 'success');
        });

        Bot::getDiscord()->on(Event::MESSAGE_UPDATE, function (Message $message, Discord $discord, ?Message $oldMessage) {
            if ($message->author?->bot) {
                return;
            }
            if (!$message->guild_id) {
                return;
            }

            $guild = Bot::get()->getGuild($message->guild_id);
            if (isset($oldMessage) && $guild->getLogSetting(LogSetting::MESSAGE_UPDATED) && count($oldMessage->embeds) === count($message->embeds)) {
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
                if (!$message->guild_id) {
                    return;
                }
                $guild = Bot::get()->getGuild($message->guild_id);
                if ($guild->getLogSetting(LogSetting::MESSAGE_DELETED)) {
                    $desc = "Message deleted in <#{$message->channel_id}>

                **Message**
                {$message->content}";
                    $guild->logWithMember($message->member, $desc, 'fail');
                }
            }
        });
    }
}
