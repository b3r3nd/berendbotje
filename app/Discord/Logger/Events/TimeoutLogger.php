<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Logger\Enums\LogSetting;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class TimeoutLogger extends DiscordEvent
{
    public function registerEvent(): void
    {
        $this->discord->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord) {
            if ($member->communication_disabled_until == NULL || $member->communication_disabled_until <= Carbon::now()) {
                return;
            }
            $localGuild = $this->bot->getGuild($member->guild_id);
            if ($localGuild->getLogSetting(LogSetting::TIMEOUT)) {
                $localGuild->logWithMember($member, "<@{$member->id}> has received a timeout", 'fail');
            }
        });

    }
}
