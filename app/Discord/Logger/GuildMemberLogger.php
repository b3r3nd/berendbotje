<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Models\BanCounter;
use App\Models\DiscordUser;
use App\Models\KickCounter;
use Discord\Discord;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class GuildMemberLogger
{
    public function __construct()
    {

        Bot::getDiscord()->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
            $guild = Bot::get()->getGuild($member->guild_id);
            $guild->logWithMember($member, "<@{$member->id}> joined the server", 'success');

        });

        Bot::getDiscord()->on(Event::GUILD_MEMBER_REMOVE, function (Member $member, Discord $discord) {
            $localGuild = Bot::get()->getGuild($member->guild_id);
            $discord->guilds->fetch($member->guild_id)->done(function (Guild $guild) use ($member, $localGuild) {
                $guild->getAuditLog(['limit' => 1])->done(function (AuditLog $auditLog) use ($member, $guild, $localGuild) {
                    foreach ($auditLog->audit_log_entries as $entry) {
                        if ($entry->action_type == 20) {
                            $localGuild->logWithMember($member, "<@{$member->id}> kicked from the server", 'fail');
                        } elseif ($entry->action_type == 22) {
                            $localGuild->logWithMember($member, "<@{$member->id}> banned from the server", 'fail');
                        } else {
                            $localGuild->logWithMember($member, "<@{$member->id}> left the server", 'fail');
                        }
                    }
                });
            });


        });

        Bot::getDiscord()->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord, ?Member $oldMember) {
            $guild = Bot::get()->getGuild($member->guild_id);
            if ($member->displayname != $oldMember->displayname) {
                $desc = "**Username changed**

                **From**
                {$oldMember->displayname}

                **To**
                {$member->displayname}";
                $guild->logWithMember($member, $desc);
            }
        });
    }

}
