<?php

namespace App\Discord\Levels\Actions;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use App\Domain\Fun\Models\RoleReward;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Exception;

class SyncRoleRewardsAction implements Action
{
    /**
     * @param Bot $bot
     * @param Message $message
     * @param string $userId
     */
    public function __construct(
        private readonly Bot     $bot,
        private readonly Message $message,
        private readonly string  $userId,
    )
    {
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute(): void
    {
        $user = User::get($this->userId);
        $messageCounter = $user->messageCounters()->where('guild_id', Guild::get($this->message->guild_id)->id)->get()->first();

        // Give role if a role reward has been reached
        $roleRewards = RoleReward::byGuild($this->message->guild_id)->get();
        if (!$roleRewards->isEmpty()) {
            foreach ($roleRewards as $reward) {
                $role = $reward->role;
                $rolesCollection = collect($this->message->member->roles);
                if (($messageCounter->level >= $reward->level) && !$rolesCollection->contains('id', $role)) {
                    try {
                        $this->message->member->addRole($role);
                    } catch (NoPermissionsException) {
                        $this->bot->getGuild($this->message->guild_id)?->log(__('bot.exception.role'), "fail");
                    }
                }
            }
        }
    }
}
