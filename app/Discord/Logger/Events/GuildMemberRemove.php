<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\GUILD_MEMBER_REMOVE;
use App\Domain\Discord\User;
use App\Domain\Moderation\Models\BanCounter;
use App\Domain\Moderation\Models\KickCounter;
use App\Domain\Setting\Enums\LogSetting;
use App\Domain\Setting\Enums\Setting;
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
                        $user = User::get($entry->user->id);
                        if ($entry->action_type === 20 && $entry->target_id === $member->id) {
                            if ($localGuild->getLogSetting(LogSetting::KICKED_SERVER)) {
                                $localGuild->logWithMember($member, __('bot.log.kicked', ['user' => $member->id]), 'fail');
                            }
                            $kickCounters = $user->kickCounters()->where('guild_id', $localGuild->model->id)->get();
                            if ($kickCounters->isEmpty()) {
                                $user->kickCounters()->save(new KickCounter(['count' => 1, 'guild_id' => $localGuild->model->id]));
                            } else {
                                $kickCounter = $kickCounters->first();
                                $kickCounter->update(['count' => $kickCounter->count + 1]);
                            }
                        } elseif ($entry->action_type === 22 && $entry->target_id === $member->id) {
                            if ($localGuild->getLogSetting(LogSetting::BANNED_SERVER)) {
                                $localGuild->logWithMember($member, __('bot.log.banned', ['user' => $member->id]), 'fail');
                            }
                            $banCounters = $user->banCounters()->where('guild_id', $localGuild->model->id)->get();
                            if ($banCounters->isEmpty()) {
                                $user->banCounters()->save(new BanCounter(['count' => 1, 'guild_id' => $localGuild->model->id]));
                            } else {
                                $banCounter = $banCounters->first();
                                $banCounter->update(['count' => $banCounter->count + 1]);
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
