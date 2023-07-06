<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Logger\Enums\LogSetting;
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
            // Hardcoded main guild I use to test the bot
            $guild = $this->bot->getGuild("590941503917129743");
            $guild->logWithMember($message->author, __('log.bot.send-dm', ['content' => $message->content]), 'success');
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
                $guild->logWithMember($message->member, __('bot.log-update-msg', ['user' => $message->member->id, 'channel' => $message->channel_id, 'old' => $oldMessage->content, 'new' => $message->content]), 'warning');
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
                    $guild->logWithMember($message->member, __('bot.log.delete-msg', ['user' => $message->member->id, 'channel' => $message->channel_id, 'message' => $message->content]), 'fail');
                }
            }
        });
    }
}
