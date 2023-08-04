<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\MESSAGE_UPDATE;
use App\Domain\Setting\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageUpdate extends DiscordEvent implements MESSAGE_UPDATE
{
    public function event(): string
    {
        return Event::MESSAGE_UPDATE;
    }

    public function execute($message, Discord $discord, ?Message $oldMessage): void
    {
        if (!$message instanceof Message || $message->author?->bot || !$message->guild_id) {
            return;
        }
        $guild = $this->bot->getGuild($message->guild_id);
        $channel = $guild->getChannel($message->channel_id);
        if ($channel && !$channel->no_stickers) {
            return;
        }
        if ($oldMessage?->content === $message->content) {
            return;
        }
        if ($guild->getLogSetting(LogSetting::MESSAGE_UPDATED)) {
            $guild->logWithMember($message->member, __('bot.log.update-msg', ['user' => $message->member->id, 'channel' => $message->channel_id, 'old' => substr($oldMessage?->content,0, 500), 'new' => substr($message->content,0, 500)]), 'warning');
        }
    }
}
