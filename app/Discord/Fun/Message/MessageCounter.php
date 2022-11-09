<?php

namespace App\Discord\Fun\Message;

use App\Discord\Core\Bot;
use App\Discord\Helper;
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
                $user = DiscordUser::get($message->author->id);
                $guild->setLastMessage($message->author->id);


                $messageCounters = $user->messageCounters()->where('guild_id', $guild->model->id)->get();
                $messageCounter = new \App\Models\MessageCounter(['count' => 1, 'guild_id' => $guild->model->id]);

                if ($messageCounters->isEmpty()) {
                    $user->messageCounters()->save($messageCounter);
                } else {
                    $messageCounter = $messageCounters->first();
                    $messageCounter->update(['count' => $messageCounter->count + 1]);
                }

                /**
                 * @TODO move code below to separate actions/class/listener or whatever
                 */

                // Also update level and XP
                $xpCount = $guild->getSetting('xp_count');
                $xp = $messageCounter->count * $xpCount;
                $level = Helper::calcLevel($messageCounter->count, $xpCount);

                $messageCounter->update([
                    'level' => $level,
                    'xp' => $xp,
                ]);

                // Give role if a role rewarch has been reached
                $roleRewards = RoleReward::where('level', $level)->get();
                if (!$roleRewards->isEmpty()) {
                    $role = $roleRewards->first()->role;
                    $test = collect($message->member->roles);
                    if (!$test->contains('id', $role)) {
                        $message->member->addRole($role)->done(function () {
                            var_dump('role given!!!');
                        });
                    }
                }
            }
        });
    }
}
