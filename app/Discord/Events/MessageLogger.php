<?php

namespace App\Discord\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageLogger extends DiscordEvent
{

    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author?->bot) {
                return;
            }
            if ($message->guild_id) {
                return;
            }

            $guild = $this->bot->getGuild("590941503917129743");
            $guild->logWithMember($message->author, "Send DM:\n\n" . $message->content, 'success');
        });

        $this->discord->on(Event::MESSAGE_UPDATE, function (Message $message, Discord $discord, ?Message $oldMessage) {
            if ($message->author?->bot) {
                return;
            }
            if (!$message->guild_id) {
                return;
            }
            $guild = $this->bot->getGuild($message->guild_id);
            $channel = $guild->getChannel($message->channel_id);
            if ($channel && !$channel->no_stickers) {
                return;
            }

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


        $this->discord->on(Event::MESSAGE_DELETE, function (object $message, Discord $discord) {
            if ($message instanceof Message) {
                if ($message->author->bot) {
                    return;
                }
                if (!$message->guild_id) {
                    return;
                }
                $guild = $this->bot->getGuild($message->guild_id);
                $channel = $guild->getChannel($message->channel_id);
                if ($channel && !$channel->no_stickers) {
                    return;
                }
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
