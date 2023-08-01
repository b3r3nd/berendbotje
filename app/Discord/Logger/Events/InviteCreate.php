<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Logger\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Channel\Invite;
use Discord\WebSockets\Event;
use Exception;

class InviteCreate extends DiscordEvent
{

    public function event(): string
    {
        return Event::INVITE_CREATE;
    }

    /**
     * @param Invite $invite
     * @param Discord $discord
     * @return void
     * @throws Exception
     */
    public function execute(Invite $invite, Discord $discord): void
    {
        $guild = $this->bot->getGuild($invite->guild_id);
        if ($guild->getLogSetting(LogSetting::INVITE_CREATED)) {
            $guild->logWithMember($invite->inviter, __('bot.log.create-invite', ['inviter' => $invite->inviter->id]), 'success');
        }
    }
}
