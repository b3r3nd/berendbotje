<?php

namespace App\Discord\Levels\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Levels\Actions\SyncRoleRewardsAction;
use App\Discord\Levels\Actions\UpdateMessageCounterAction;
use App\Domain\Discord\Channel;
use App\Domain\Discord\User;
use App\Domain\Setting\Enums\Setting;
use App\Domain\Setting\Enums\UserSetting;
use Carbon\Carbon;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

class MessageXpCounter implements MessageCreateAction
{
    /**
     * @throws NoPermissionsException
     */
    public function execute(Bot $bot, Guild $guildModel, Message $message, ?Channel $channel): void
    {
        if (($channel && $channel->no_xp) || !$guildModel->getSetting(Setting::ENABLE_XP)) {
            return;
        }
        $lastMessageDate = $guildModel->getLastMessage($message->author->id);
        if ($lastMessageDate->diffInSeconds(Carbon::now()) >= $guildModel->getSetting(Setting::XP_COOLDOWN)) {
            $guildModel->setLastMessage($message->author->id);
            (new UpdateMessageCounterAction($message->guild_id, $message->author->id, $guildModel->getSetting(Setting::XP_COUNT), $bot))->execute();

            $user = User::get($message->author->id);
            if ($guildModel->getSetting(Setting::ENABLE_ROLE_REWARDS) && !$user->enabledSetting(UserSetting::NO_ROLE_REWARDS->value, $guildModel->model->id)) {
                (new SyncRoleRewardsAction($bot, $message, $message->author->id))->execute();
            }
        }
    }
}
