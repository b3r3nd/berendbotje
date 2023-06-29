<?php

namespace App\Discord\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Enums\Setting;
use App\Discord\Levels\SyncRoleRewardsAction;
use App\Discord\Levels\UpdateMessageCounterAction;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageXpCounter extends DiscordEvent
{
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            if (!$message->guild_id) {
                return;
            }

            $guild = $this->bot->getGuild($message->guild_id);
            if ($guild) {
                if (!$guild->getSetting(Setting::ENABLE_XP)) {
                    return;
                }
                $channel = $guild->getChannel($message->channel_id);
                if ($channel && $channel->no_xp) {
                    return;
                }

                $lastMessageDate = $guild->getLastMessage($message->author->id);
                if ($lastMessageDate->diffInSeconds(Carbon::now()) >= $guild->getSetting(Setting::XP_COOLDOWN)) {
                    $guild->setLastMessage($message->author->id);
                    (new UpdateMessageCounterAction($message->guild_id, $message->author->id, $guild->getSetting(Setting::XP_COUNT), $this->bot))->execute();

                    if ($guild->getSetting(Setting::ENABLE_ROLE_REWARDS)) {
                        (new SyncRoleRewardsAction($message, $message->author->id))->execute();
                    }
                }
            }
        });
    }
}
