<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageCounter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            $guild = Bot::get()->getGuild($message->guild_id);
            if ($guild) {
                if (!$guild->getSetting(Setting::ENABLE_XP)) {
                    return;
                }

                $lastMessageDate = $guild->getLastMessage($message->author->id);
                if ($lastMessageDate->diffInSeconds(Carbon::now()) >= $guild->getSetting(Setting::XP_COOLDOWN)) {
                    $guild->setLastMessage($message->author->id);
                    (new UpdateMessageCounterAction($message, $message->author->id, $guild->getSetting(Setting::XP_COUNT)))->execute();

                    if ($guild->getSetting(Setting::ENABLE_ROLE_REWARDS)) {
                        (new SyncRoleRewardsAction($message, $message->author->id))->execute();
                    }
                }
            }
        });
    }
}
