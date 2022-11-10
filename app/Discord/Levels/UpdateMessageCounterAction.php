<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Discord\Helper;
use App\Models\DiscordUser;
use Discord\Parts\Channel\Message;


class UpdateMessageCounterAction implements Action
{
    private Message $message;
    private string $userId;
    private int $xpCount;
    private bool $removeXp;

    /**
     * @param Message $message
     * @param $userId
     * @param $xpCount
     * @param $removeXp
     */
    public function __construct(Message $message, $userId, $xpCount, $removeXp = false)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->xpCount = $xpCount;
        $this->removeXp = $removeXp;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $user = DiscordUser::get($this->userId);
        $guild = Bot::get()->getGuild($this->message->guild_id);

        $messageCounters = $user->messageCounters()->where('guild_id', $guild->model->id)->get();
        $messageCounter = new \App\Models\MessageCounter([
            'count' => 1,
            'guild_id' => $guild->model->id,
            'xp' => $this->xpCount,
        ]);

        if ($messageCounters->isEmpty()) {
            $user->messageCounters()->save($messageCounter);
        } else {
            $messageCounter = $messageCounters->first();
            $messageCounter->update(['count' => $messageCounter->count + 1]);

            if ($this->removeXp) {
                $messageCounter->update(['xp' => $messageCounter->xp - $this->xpCount]);
            } else {
                $messageCounter->update(['xp' => $messageCounter->xp + $this->xpCount]);
            }
        }
        $messageCounter->update(['level' => Helper::calcLevel($messageCounter->xp)]);
    }
}
