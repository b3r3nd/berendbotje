<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Invite;
use Discord\WebSockets\Event;

class InviteLogger
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::INVITE_CREATE, function (Invite $invite, Discord $discord) {
            $guild = Bot::get()->getGuild($invite->guild_id);
            $guild->logWithMember($invite->inviter, "<@{$invite->inviter->id}> created a new invite link", 'success');
        });

        Bot::getDiscord()->on(Event::INVITE_DELETE, function (object $invite, Discord $discord) {
            if ($invite instanceof Invite) {
                $guild = Bot::get()->getGuild($invite->guild_id);
                $guild->logWithMember($invite->inviter, "Invite link by <@{$invite->inviter->id}> removed", 'fail');
            }
        });
    }
}
