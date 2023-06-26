<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Ban;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class GuildMemberLogger
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
            $guild = Bot::get()->getGuild($member->guild_id);
            if ($guild->getLogSetting(LogSetting::JOINED_SERVER)) {
                $guild->logWithMember($member, "<@{$member->id}> joined the server", 'success');
            }
        });

        Bot::getDiscord()->on(Event::GUILD_MEMBER_REMOVE, function (Member $member, Discord $discord) {
            $localGuild = Bot::get()->getGuild($member->guild_id);
            $discord->guilds->fetch($member->guild_id)->done(function (Guild $guild) use ($member, $localGuild) {
                $guild->getAuditLog(['limit' => 1])->done(function (AuditLog $auditLog) use ($member, $guild, $localGuild) {
                    foreach ($auditLog->audit_log_entries as $entry) {
                        if ($entry->action_type === 20 && $entry->target_id === $member->id) {
                            if ($localGuild->getLogSetting(LogSetting::KICKED_SERVER)) {
                                $localGuild->logWithMember($member, "<@{$member->id}> kicked from the server", 'fail');
                            }
                        } elseif ($entry->action_type === 22 && $entry->target_id === $member->id) {
                            if ($localGuild->getLogSetting(LogSetting::BANNED_SERVER)) {
                                $localGuild->logWithMember($member, "<@{$member->id}> banned from the server", 'fail');
                            }
                        } else if ($localGuild->getLogSetting(LogSetting::LEFT_SERVER)) {
                            $localGuild->logWithMember($member, "<@{$member->id}> left the server", 'fail');
                        }
                    }
                });
            });
        });

        Bot::getDiscord()->on(Event::GUILD_BAN_REMOVE, function (Ban $ban, Discord $discord) {
            $guild = Bot::get()->getGuild($ban->guild_id);
            if ($guild->getLogSetting(LogSetting::UNBANNED_SERVER)) {
                $guild->logWithMember($ban->user, "<@{$ban->user_id}> was unbanned from the server", 'success');
            }
        });

        Bot::getDiscord()->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord, ?Member $oldMember) {
            if (!$oldMember) {
                return;
            }
            $guild = Bot::get()->getGuild($member->guild_id);
            if ($guild?->getLogSetting(LogSetting::UPDATED_USERNAME)) {
                if ($member->displayname !== $oldMember->displayname) {
                    $desc = "**Username changed**

                **From**
                {$oldMember->displayname}

                **To**
                {$member->displayname}";
                    $guild->logWithMember($member, $desc);
                }
            }
        });
    }

}
