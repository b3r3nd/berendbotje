<?php

namespace App\Discord\Statistics;

use App\Discord\Core\Bot;
use App\Models\BanCounter;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use App\Models\KickCounter;
use Discord\Discord;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

/**
 * action_type 20 = kick
 * action_type 22 = ban
 * action_type 25 = leave -> we ignore this
 */
class DetectKicksAndBans
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::GUILD_MEMBER_REMOVE, function (Member $member, Discord $discord) {
            $discord->guilds->fetch($member->guild_id)->done(function (Guild $guild) use ($member) {
                $guild->getAuditLog(['limit' => 1])->done(function (AuditLog $auditLog) use ($member) {
                    foreach ($auditLog->audit_log_entries as $entry) {
                        $user = DiscordUser::firstOrCreate(['discord_id' => $entry->user->id]);
                        if ($entry->action_type == 20) {
                            if ($user->kickCounter) {
                                $user->kickCounter()->update(['count' => $user->kickCounter->count + 1]);
                            } else {
                                $user->kickCounter()->save(new KickCounter(['count' => 1]));
                            }
                        } elseif ($entry->action_type == 22) {
                            if ($user->banCounter) {
                                $user->banCounter()->update(['count' => $user->banCounter->count + 1]);
                            } else {
                                $user->banCounter()->save(new BanCounter(['count' => 1]));
                            }
                        }
                    }
                });
            });
        });
    }
}
