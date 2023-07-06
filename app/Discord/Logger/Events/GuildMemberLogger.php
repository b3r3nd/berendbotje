<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Logger\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Ban;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class GuildMemberLogger extends DiscordEvent
{
    public function registerEvent(): void
    {
        $this->discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
            $guild = $this->bot->getGuild($member->guild_id);
            if ($guild->getLogSetting(LogSetting::JOINED_SERVER)) {
                $guild->logWithMember($member, __('bot.log.joined', ['user' => $member->id]), 'success');
            }
        });

        $this->discord->on(Event::GUILD_MEMBER_REMOVE, function (Member $member, Discord $discord) {
            $localGuild = $this->bot->getGuild($member->guild_id);
            $discord->guilds->fetch($member->guild_id)->done(function (Guild $guild) use ($member, $localGuild) {
                $guild->getAuditLog(['limit' => 1])->done(function (AuditLog $auditLog) use ($member, $guild, $localGuild) {
                    foreach ($auditLog->audit_log_entries as $entry) {
                        if ($entry->action_type === 20 && $entry->target_id === $member->id) {
                            if ($localGuild->getLogSetting(LogSetting::KICKED_SERVER)) {
                                $localGuild->logWithMember($member, __('bot.log.kicked', ['user' => $member->id]), 'fail');
                            }
                        } elseif ($entry->action_type === 22 && $entry->target_id === $member->id) {
                            if ($localGuild->getLogSetting(LogSetting::BANNED_SERVER)) {
                                $localGuild->logWithMember($member, __('bot.log.banned', ['user' => $member->id]), 'fail');
                            }
                        } else if ($localGuild->getLogSetting(LogSetting::LEFT_SERVER)) {
                            $localGuild->logWithMember($member, __('bot.log.left', ['user' => $member->id]), 'fail');
                        }
                    }
                });
            });
        });

        $this->discord->on(Event::GUILD_BAN_REMOVE, function (Ban $ban, Discord $discord) {
            $guild = $this->bot->getGuild($ban->guild_id);
            if ($guild->getLogSetting(LogSetting::UNBANNED_SERVER)) {
                $guild->logWithMember($ban->user, __('bot.log.joined', ['user' => $ban->user_id]), 'success');
            }
        });

        $this->discord->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord, ?Member $oldMember) {
            if (!$oldMember) {
                return;
            }
            $guild = $this->bot->getGuild($member->guild_id);
            if ($guild?->getLogSetting(LogSetting::UPDATED_USERNAME)) {
                if ($member->displayname !== $oldMember->displayname) {
                    $guild->logWithMember($member, __('bot.log.username-change', ['from' => $oldMember->displayname, 'to' => $member->displayname]));
                }
            }
        });
    }
}
