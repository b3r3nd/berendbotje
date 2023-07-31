<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Logger\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Channel\Invite;
use Discord\WebSockets\Event;
use Exception;

class InviteDelete extends DiscordEvent
{
    public function event(): string
    {
        return Event::INVITE_DELETE;
    }

    /**
     * @param object $invite
     * @param Discord $discord
     * @return void
     * @throws Exception
     */
    public function execute(object $invite, Discord $discord): void
    {
        if ($invite instanceof Invite) {
            $guild = $this->bot->getGuild($invite->guild_id);
            if ($guild->getLogSetting(LogSetting::INVITE_REMOVED)) {
                $guild->logWithMember($invite->inviter, __('bot.log.remove-invite', ['inviter' => $invite->inviter->id]), 'fail');
            }
        }
    }
}
