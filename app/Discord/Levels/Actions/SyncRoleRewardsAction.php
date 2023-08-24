<?php

namespace App\Discord\Levels\Actions;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Discord\Levels\Helpers\Helper;
use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use App\Domain\Fun\Models\RoleReward;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Exception;
use Illuminate\Support\Carbon;

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
     * @param string $role
     * @return void
     * @throws Exception
     */
    private function giveRole(string $role): void
    {
        try {
            $this->message->member->addRole($role);
        } catch (NoPermissionsException) {
            $this->bot->getGuild($this->message->guild_id)?->log(__('bot.exception.role'), "fail");
        }
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

                if (!$rolesCollection->contains('id', $role)) {
                    if ($reward->level && ($messageCounter->level >= $reward->level)) {
                        $this->giveRole($role);
                    }
                    if ($reward->duration) {
                        $matches = Helper::match($reward->duration);
                        $date = Helper::getDate($matches);
                        $joinedAt = Helper::parse($this->message->member->joined_at);
                        if ($joinedAt->lt($date)) {
                            $this->giveRole($role);
                        }
                    }
                }
            }
        }
    }
}
