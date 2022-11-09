<?php

namespace App\Discord\Levels\Actions;

use App\Discord\Core\Bot;
use App\Discord\Helper;
use App\Models\DiscordUser;
use Discord\Parts\Channel\Message;

/**
 * Action to process everything related to XP and levels.
 */
class ProcessMessageCounterAction
{
    public function execute(Message $message, $userId, $xpCount, $removeXp = false): void
    {
        $user = DiscordUser::get($userId);
        $guild = Bot::get()->getGuild($message->guild_id);

        $messageCounters = $user->messageCounters()->where('guild_id', $guild->model->id)->get();
        $messageCounter = new \App\Models\MessageCounter([
            'count' => 1,
            'guild_id' => $guild->model->id,
            'xp' => $xpCount,
        ]);

        if ($messageCounters->isEmpty()) {
            $user->messageCounters()->save($messageCounter);
        } else {
            $messageCounter = $messageCounters->first();
            $messageCounter->update(['count' => $messageCounter->count + 1]);

            if ($removeXp) {
                $messageCounter->update(['xp' => $messageCounter->xp - $xpCount]);
            } else {
                $messageCounter->update(['xp' => $messageCounter->xp + $xpCount]);
            }
        }
        $messageCounter->update(['level' => Helper::calcLevel($messageCounter->xp)]);
    }
}
