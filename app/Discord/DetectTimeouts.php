<?php

namespace App\Discord;

use App\Models\Admin;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class DetectTimeouts
{

    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord) use ($bot) {
            $endTime = $member->communication_disabled_until;
            $startTime = Carbon::now();
            if ($endTime) {
                $diff = $endTime->diffInMinutes($startTime);
                \App\Models\Timeout::create([
                    'discord_id' => $member->id,
                    'discord_username' => $member->username,
                    'length' => $diff,
                ]);
            }
        });
    }
}
