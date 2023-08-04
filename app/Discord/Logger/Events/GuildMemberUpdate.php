<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\GUILD_MEMBER_UPDATE;
use App\Domain\Setting\Enums\LogSetting;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use Exception;

class GuildMemberUpdate extends DiscordEvent implements GUILD_MEMBER_UPDATE
{
    public function event(): string
    {
        return Event::GUILD_MEMBER_UPDATE;
    }

    /**
     * @param Member $member
     * @param Discord $discord
     * @param Member|null $oldMember
     * @return void
     * @throws Exception
     */
    public function execute(Member $member, Discord $discord, ?Member $oldMember): void
    {
        if (!$oldMember) {
            return;
        }
        $guild = $this->bot->getGuild($member->guild_id);
        if ($guild?->getLogSetting(LogSetting::UPDATED_USERNAME)) {
            if ($member->displayname !== $oldMember->displayname) {
                $guild->logWithMember($member, __('bot.log.username-change', ['from' => $oldMember->displayname, 'to' => $member->displayname]));
            }
            if ($member->communication_disabled_until == NULL || $member->communication_disabled_until <= Carbon::now()) {
                return;
            }
            $localGuild = $this->bot->getGuild($member->guild_id);
            if ($localGuild->getLogSetting(LogSetting::TIMEOUT)) {
                $localGuild->logWithMember($member, __('bot.log.timeout', ['user' => $member->id]), 'fail');
            }
        }
    }
}
