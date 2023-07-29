<?php

namespace App\Discord\Levels\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Enums\UserSetting;
use App\Discord\Core\Guild;
use App\Discord\Core\MessageCreateEvent;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Levels\Actions\SyncRoleRewardsAction;
use App\Discord\Levels\Actions\UpdateMessageCounterAction;
use App\Models\Channel;
use Carbon\Carbon;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

class MessageXpCounter implements MessageCreateEvent
{
    public function execute(Bot $bot, Guild $guild, Message $message, ?Channel $channel): void
    {
        if (($channel && $channel->no_xp) || !$guild->getSetting(Setting::ENABLE_XP)) {
            return;
        }
        $lastMessageDate = $guild->getLastMessage($message->author->id);
        if ($lastMessageDate->diffInSeconds(Carbon::now()) >= $guild->getSetting(Setting::XP_COOLDOWN)) {
            $guild->setLastMessage($message->author->id);
            (new UpdateMessageCounterAction($message->guild_id, $message->author->id, $guild->getSetting(Setting::XP_COUNT), $bot))->execute();

            $user = DiscordUser::get($message->author->id);
            if ($guild->getSetting(Setting::ENABLE_ROLE_REWARDS) && !$user->enabledSetting(UserSetting::NO_ROLE_REWARDS->value, $message->guild_id)) {
                (new SyncRoleRewardsAction($message, $message->author->id))->execute();
            }
        }
    }
}
