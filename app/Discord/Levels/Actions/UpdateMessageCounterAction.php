<?php

namespace App\Discord\Levels\Actions;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Interfaces\Action;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Levels\Helpers\Helper;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;


class UpdateMessageCounterAction implements Action
{
    private string $userId;
    private int $xpCount;
    private bool $removeXp;
    private string $guildId;
    private Bot $bot;

    /**
     * @param string $guildId
     * @param $userId
     * @param $xpCount
     * @param bool $removeXp
     * @param Bot $bot
     */
    public function __construct(string $guildId, $userId, $xpCount, Bot $bot, bool $removeXp = false)
    {
        $this->userId = $userId;
        $this->xpCount = $xpCount;
        $this->removeXp = $removeXp;
        $this->guildId = $guildId;
        $this->bot = $bot;
    }

    /**
     * @return void
     * @throws NoPermissionsException
     */
    public function execute(): void
    {
        $user = DiscordUser::get($this->userId);
        $guild = $this->bot->getGuild($this->guildId);

        $messageCounters = $user->messageCounters()->where('guild_id', $guild->model->id)->get();
        $messageCounter = new \App\Discord\Levels\Models\UserXP([
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
                $oldLevel = Helper::calcLevel($messageCounter->xp);
                $messageCounter->update(['xp' => $messageCounter->xp + $this->xpCount]);
                $newLevel = Helper::calcLevel($messageCounter->xp);

                if ($newLevel > $oldLevel && $guild->getSetting(Setting::ENABLE_LVL_MSG)) {
                    $member = $this->bot->discord->guilds->get('id', $this->guildId)->members->get('id', $messageCounter->user->discord_id);
                    $this->bot->discord->getChannel($guild->getSetting(Setting::LEVEL_UP_CHAN))?->sendMessage("Congrats {$member?->username} for reaching level {$newLevel}");
                }
            }
        }

        $messageCounter->update(['level' => Helper::calcLevel($messageCounter->xp)]);

    }
}
