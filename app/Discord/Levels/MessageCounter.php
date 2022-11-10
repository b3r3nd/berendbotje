<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
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
                $lastMessageDate = $guild->getLastMessage($message->author->id);
                if ($lastMessageDate->diffInSeconds(Carbon::now()) >= $guild->getSetting('xp_cooldown')) {
                    $guild->setLastMessage($message->author->id);
                    (new UpdateMessageCounterAction($message, $message->author->id, $guild->getSetting('xp_count')))->execute();
                    (new SyncRoleRewardsAction($message, $message->author->id))->execute();
                }
            }
        });
    }
}
