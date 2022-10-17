<?php

namespace App\Discord;

use App\Discord\Core\Bot;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class DetectTimeouts
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord) use ($bot) {
            $discord->guilds->fetch('590941503917129743')->done(function (Guild $guild) use ($member) {
                $guild->getAuditLog(['limit' => 1])->done(function (AuditLog $auditLog) use ($member) {
                    foreach ($auditLog->audit_log_entries as $entry) {
                        $endTime = $member->communication_disabled_until;
                        $startTime = Carbon::now();
                        if ($endTime) {
                            $diff = $endTime->diffInMinutes($startTime);
                            \App\Models\Timeout::create([
                                'discord_id' => $member->id,
                                'discord_username' => $member->username,
                                'length' => $diff,
                                'reason' => $entry->reason,
                            ]);
                        }
                    }
                });
            });
        });
    }
}
