<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class TimeoutLogger
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord) {
            if ($member->communication_disabled_until == NULL || $member->communication_disabled_until <= Carbon::now()) {
                return;
            }
            $localGuild = Bot::get()->getGuild($member->guild_id);
            $localGuild->logWithMember($member, "<@{$member->id}> has received a timeout", 'fail');
        });

    }


}
