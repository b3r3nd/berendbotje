<?php

namespace App\Discord\Timeout;

use App\Discord\Core\Bot;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

/**
 * Well, it does detect the timeouts given by moderators however it's a bit cumbersome. We would like to have the
 * reason of the timeout added to our backlog. This info is not given to us when the member who received the timeout
 * is updated. We have to manually check the last entry in the audit log in order to get the reason.
 *
 * @TODO RemoveSong hardcoded server ID
 * @TODO Read the actual right entry, it now reads the latest which - should - be OK, but there are cases where it wont work.
 */
class DetectTimeouts
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord) {
            if ($member->communication_disabled_until == NULL) {
                return;
            }
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
