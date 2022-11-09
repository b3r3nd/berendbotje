<?php

namespace App\Discord\Levels\Actions;

use App\Discord\Core\Bot;
use App\Models\DiscordUser;
use App\Models\Guild;
use App\Models\RoleReward;
use Discord\Parts\Channel\Message;

class ProcessRoleRewardsAction
{
    public function execute(Message $message, $userId)
    {
        $user = DiscordUser::get($userId);
        $messageCounter = $user->messageCounters()->where('guild_id', Guild::get($message->guild_id)->id)->get()->first();

        // Give role if a role reward has been reached
        $roleRewards = RoleReward::byGuild($message->guild_id)->get();
        if (!$roleRewards->isEmpty()) {
            foreach ($roleRewards as $reward) {
                $role = $reward->role;
                if ($messageCounter->level >= $reward->level) {
                    $rolesCollection = collect($message->member->roles);
                    if (!$rolesCollection->contains('id', $role)) {
                        $message->member->addRole($role)->done(function () {
                            var_dump('role given!!!');
                        });
                    }
                } else {
                    $rolesCollection = collect($message->member->roles);
                    if ($rolesCollection->contains('id', $role)) {
                        $message->member->removeRole($role)->done(function () {
                            var_dump('role removed!!!');
                        });
                    }
                }
            }
        }
    }
}
