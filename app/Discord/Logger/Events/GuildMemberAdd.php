<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\GUILD_MEMBER_ADD;
use App\Domain\Setting\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use Exception;

class GuildMemberAdd extends DiscordEvent implements GUILD_MEMBER_ADD
{
    public function event(): string
    {
        return Event::GUILD_MEMBER_ADD;
    }

    /**
     * @param Member $member
     * @param Discord $discord
     * @return void
     * @throws Exception
     */
    public function execute(Member $member, Discord $discord): void
    {
        $guild = $this->bot->getGuild($member->guild_id);
        if ($guild->getLogSetting(LogSetting::JOINED_SERVER)) {
            $guild->logWithMember($member, __('bot.log.joined', ['user' => $member->id]), 'success');
        }
    }
}
