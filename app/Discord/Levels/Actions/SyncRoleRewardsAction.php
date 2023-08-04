<?php

namespace App\Discord\Levels\Actions;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use App\Discord\Levels\Models\RoleReward;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Exception;

/**
 * @property Message $message   Message instance which triggered this event.
 * @property string $userId     Discord User id of the user sending the message.
 */
class SyncRoleRewardsAction implements Action
{
    private Message $message;
    private string $userId;
    private Bot $bot;

    /**
     * @param Bot $bot
     * @param Message $message
     * @param $userId
     */
    public function __construct(Bot $bot, Message $message, $userId)
    {
        $this->bot = $bot;
        $this->message = $message;
        $this->userId = $userId;
    }

    /**
     * @return void
     * @throws Exception
     */
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
                if (($messageCounter->level >= $reward->level) && !$rolesCollection->contains('id', $role)) {
                    try {
                        $this->message->member->addRole($role);
                    } catch(NoPermissionsException) {
                        $this->bot->getGuild($this->message->guild_id)?->log(__('bot.exception.role'), "fail");
                    }
                }
            }
        }
    }
}
