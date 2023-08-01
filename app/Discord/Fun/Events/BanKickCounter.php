<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Fun\Models\BanCounter;
use App\Discord\Fun\Models\KickCounter;
use Discord\Discord;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use Exception;

/**
 * This event triggers for whenever somebody leaves, gets kicked or gets banned. We need to read the audit log
 * to figure out what has happened, luckily we can determine the action based on action_type.
 *
 * action_type 20 = kick
 * action_type 22 = ban
 * action_type 25 = leave -> we ignore this
 */
class BanKickCounter extends DiscordEvent
{
    public function event(): string
    {
        return Event::GUILD_MEMBER_REMOVE;
    }

    /**
     * @param Member $member
     * @param Discord $discord
     * @return void
     * @throws Exception
     */
    public function execute(Member $member, Discord $discord): void
    {
            $discord->guilds->fetch($member->guild_id)->done(function (Guild $guild) use ($member) {
                $guild->getAuditLog(['limit' => 1])->done(function (AuditLog $auditLog) use ($member, $guild) {
                    foreach ($auditLog->audit_log_entries as $entry) {
                        $user = DiscordUser::get($entry->user->id);
                        $guildModel = \App\Discord\Core\Models\Guild::get($guild->id);
                        if ($entry->action_type == 20) {
                            $kickCounters = $user->kickCounters()->where('guild_id', $guildModel->id)->get();
                            if ($kickCounters->isEmpty()) {
                                $user->kickCounters()->save(new KickCounter(['count' => 1, 'guild_id' => $guildModel->id]));
                            } else {
                                $kickCounter = $kickCounters->first();
                                $kickCounter->update(['count' => $kickCounter->count + 1]);
                            }
                        } elseif ($entry->action_type == 22) {
                            $banCounters = $user->banCounters()->where('guild_id', $guildModel->id)->get();
                            if ($banCounters->isEmpty()) {
                                $user->banCounters()->save(new BanCounter(['count' => 1, 'guild_id' => $guildModel->id]));
                            } else {
                                $banCounter = $banCounters->first();
                                $banCounter->update(['count' => $banCounter->count + 1]);
                            }
                        }
                    }
                });
            });

    }
}
