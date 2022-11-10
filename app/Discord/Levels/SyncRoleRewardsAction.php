<?php

namespace App\Discord\Levels;

use App\Discord\Core\Action;
use App\Models\DiscordUser;
use App\Models\Guild;
use App\Models\RoleReward;
use Discord\Parts\Channel\Message;

class SyncRoleRewardsAction implements Action
{
    private Message $message;
    private string $userId;

    public function __construct(Message $message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }


    public function execute(): void
    {
        $user = DiscordUser::get($this->userId);
        $messageCounter = $user->messageCounters()->where('guild_id', Guild::get($this->message->guild_id)->id)->get()->first();

        // Give role if a role reward has been reached
        $roleRewards = RoleReward::byGuild($this->message->guild_id)->get();
        if (!$roleRewards->isEmpty()) {
            foreach ($roleRewards as $reward) {
                $role = $reward->role;
                $rolesCollection = collect($this->message->member->roles);
                if ($messageCounter->level >= $reward->level) {
                    if (!$rolesCollection->contains('id', $role)) {
                        $this->message->member->addRole($role)->done(function () {
                            var_dump('role given!!!');
                        });
                    }
                } else {
                    if ($rolesCollection->contains('id', $role)) {
                        $this->message->member->removeRole($role)->done(function () {
                            var_dump('role removed!!!');
                        });
                    }
                }
            }
        }
    }
}
