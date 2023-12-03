<?php

namespace App\Discord\Levels\Actions;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use App\Domain\Moderation\Helpers\DurationHelper;
use App\Domain\Moderation\Models\RoleReward;
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
     * @param string $role
     * @return void
     * @throws Exception
     */
    public function removeRole(string $role): void
    {
        try {
            $this->message->member->removeRole($role);
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
        $roleReward = RoleReward::byGuild($this->message->guild_id)->where('level', $messageCounter->level)->get()->first();
        $rolesCollection = collect($this->message->member->roles);

        // Sync the role level rewards
        if ($roleReward && !$rolesCollection->contains('id', $roleReward->role)) {
            try {
                $this->message->member->addRole($roleReward->role)->done(function () use ($messageCounter, $rolesCollection) {
                    foreach (RoleReward::byGuild($this->message->guild_id)->where('level', '!=', $messageCounter->level)->get() as $reward) {
                        try {
                            if ($rolesCollection->contains('id', $reward->role)) {
                                $this->message->member->removeRole($reward->role);
                            }
                        } catch (NoPermissionsException) {
                            $this->bot->getGuild($this->message->guild_id)?->log(__('bot.exception.role'), "fail");
                        }
                    }
                });
            } catch (NoPermissionsException) {
                $this->bot->getGuild($this->message->guild_id)?->log(__('bot.exception.role'), "fail");
            }
        }

        // Sync the role duration rewards
        foreach (RoleReward::byGuild($this->message->guild_id)->get() as $reward) {
            if ($reward->duration) {
                $matches = DurationHelper::match($reward->duration);
                $date = DurationHelper::getDate($matches);
                $joinedAt = DurationHelper::parse($this->message->member->joined_at);
                if ($joinedAt->lt($date)) {
                    $this->giveRole($reward->role);
                }
            }
        }
    }
}
