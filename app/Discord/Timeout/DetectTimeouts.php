<?php

namespace App\Discord\Timeout;

use App\Discord\Core\Bot;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

/**
 * Well, it does detect the timeouts given by moderators however it's a bit cumbersome. We would like to have the
 * reason of the timeout added to our backlog. This info is not given to us when the member who received the timeout
 * is updated. We have to manually check the last entry in the audit log in order to get the reason.
 *
 * This event triggers whenever a user is updated, when communication_disabled_until is set, and the date is not in the past
 * it means the user got timed out. If a user is never timed out the value will be null, if a user has been timed out
 * before it will be set to date of the previous timeout.
 *
 */
class DetectTimeouts
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord) {
            if ($member->communication_disabled_until == NULL || $member->communication_disabled_until <= Carbon::now()) {
                return;
            }

            $discord->guilds->fetch($member->guild_id)->done(function (Guild $guild) use ($member) {
                $guild->getAuditLog(['limit' => 1])->done(function (AuditLog $auditLog) use ($member) {
                    foreach ($auditLog->audit_log_entries as $entry) {
                        $endTime = $member->communication_disabled_until;
                        $startTime = Carbon::now();
                        if ($endTime) {
                            $diff = $endTime->diffInMinutes($startTime);
                            \App\Models\Timeout::create([
                                'discord_id' => $member->id,
                                'discord_username' => $member->username,
                                'length' => $diff ?? 0,
                                'reason' => $entry->reason ?? "Empty",
                            ]);
                        }
                    }
                });
            });
        });
    }
}
