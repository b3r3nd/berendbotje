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

/**
 * @property string $userId     Discord user id of the user whose XP is being modified.
 * @property int $xpCount       Amount of XP to change.
 * @property bool $removeXp     If the XP should be removed instead of added to the user.
 * @property string $guildId    Discord guild id it belongs to.
 * @property Bot $bot           Main bot instance
 */
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

                /**
                 * Hardcoded for now, will make proper commands to change these for each server :)
                 */
                if ($newLevel > $oldLevel && $guild->getSetting(Setting::ENABLE_LVL_MSG)) {
                    $msg = 'default-1';
                    if ($newLevel > 20) {
                        $msg = 'default-20';
                    }
                    if ($newLevel > 30) {
                        $msg = 'default-30';
                    }
                    if ($newLevel > 40) {
                        $msg = 'default-40';
                    }
                    if ($newLevel > 50) {
                        $msg = 'default-50';
                    }
                    if ($newLevel === 1 || $newLevel === 5 || $newLevel === 10 || $newLevel === 20 || $newLevel === 30 || $newLevel === 40 || $newLevel === 50 || $newLevel === 60 || $newLevel === 70) {
                        $msg = $newLevel;
                    }
                    $message = __("bot.lvlup.{$msg}", ['user' => $messageCounter->user->discord_id, 'level' => $newLevel]);
                    $this->bot->discord->getChannel($guild->getSetting(Setting::LEVEL_UP_CHAN))?->sendMessage($message);
                }
            }
        }
        $messageCounter->update(['level' => Helper::calcLevel($messageCounter->xp)]);
    }
}
