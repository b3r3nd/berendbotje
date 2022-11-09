<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Helper;
use App\Discord\Levels\Actions\ProcessMessageCounterAction;
use App\Discord\Levels\Actions\ProcessRoleRewardsAction;
use App\Models\DiscordUser;
use App\Models\RoleReward;
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
            $lastMessageDate = Bot::get()->getGuild($message->guild_id)->getLastMessage($message->author->id);
            $guild = Bot::get()->getGuild($message->guild_id);

            if ($lastMessageDate->diffInSeconds(Carbon::now()) >= $guild->getSetting('xp_cooldown')) {
                (new ProcessMessageCounterAction())->execute($message, $message->author->id, $guild->getSetting('xp_count'));
                (new ProcessRoleRewardsAction())->execute($message, $message->author->id);
            }
        });
    }
}
