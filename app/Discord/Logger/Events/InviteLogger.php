<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Logger\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Channel\Invite;
use Discord\WebSockets\Event;

class InviteLogger extends DiscordEvent
{
    public function registerEvent(): void
    {
        $this->discord->on(Event::INVITE_CREATE, function (Invite $invite, Discord $discord) {
            $guild = $this->bot->getGuild($invite->guild_id);
            if ($guild->getLogSetting(LogSetting::INVITE_CREATED)) {
                $guild->logWithMember($invite->inviter, __('bot.log.create-invite', ['inviter' => $invite->inviter->id]), 'success');
            }
        });

        $this->discord->on(Event::INVITE_DELETE, function (object $invite, Discord $discord) {
            if ($invite instanceof Invite) {
                $guild = $this->bot->getGuild($invite->guild_id);
                if ($guild->getLogSetting(LogSetting::INVITE_REMOVED)) {
                    $guild->logWithMember($invite->inviter, __('bot.log.remove-invite', ['inviter' => $invite->inviter->id]), 'fail');
                }
            }
        });
    }
}
