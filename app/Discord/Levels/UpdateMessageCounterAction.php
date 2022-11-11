<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Discord\Helper;
use App\Models\DiscordUser;
use Discord\Parts\Channel\Message;


class UpdateMessageCounterAction implements Action
{
    private string $userId;
    private int $xpCount;
    private bool $removeXp;
    private string $guildId;

    /**
     * @param string $guildId
     * @param $userId
     * @param $xpCount
     * @param bool $removeXp
     */
    public function __construct(string $guildId, $userId, $xpCount, bool $removeXp = false)
    {
        $this->userId = $userId;
        $this->xpCount = $xpCount;
        $this->removeXp = $removeXp;
        $this->guildId = $guildId;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $user = DiscordUser::get($this->userId);
        $guild = Bot::get()->getGuild($this->guildId);

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
