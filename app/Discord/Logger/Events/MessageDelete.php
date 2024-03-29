<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\MESSAGE_DELETE;
use App\Domain\Setting\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Exception;

class MessageDelete extends DiscordEvent implements MESSAGE_DELETE
{
    public function event(): string
    {
        return Event::MESSAGE_DELETE;
    }

    /**
     * @param object $message
     * @param Discord $discord
     * @return void
     * @throws Exception
     */
    public function execute(object $message, Discord $discord): void
    {
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
    }
}
