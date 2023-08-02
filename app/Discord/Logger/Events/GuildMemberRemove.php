<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\GUILD_MEMBER_REMOVE;
use App\Discord\Logger\Enums\LogSetting;
use Discord\Discord;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use Exception;

class GuildMemberRemove extends DiscordEvent implements GUILD_MEMBER_REMOVE
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
        $localGuild = $this->bot->getGuild($member->guild_id);
        try {
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
        } catch (NoPermissionsException) {
            $this->bot->getGuild($member->guild_id)?->log(__('bot.exception.audit'), "fail");
        }
    }
}
