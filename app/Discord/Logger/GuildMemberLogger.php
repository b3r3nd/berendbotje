<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class GuildMemberLogger
{
    public function __construct()
    {

        Bot::getDiscord()->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
            $guild = Bot::get()->getGuild($member->guild_id);
            $guild->log("<@{$member->id}> joined the server.", "Joined Server");

        });

        Bot::getDiscord()->on(Event::GUILD_MEMBER_REMOVE, function (Member $member, Discord $discord) {
            $guild = Bot::get()->getGuild($member->guild_id);
            $guild->log("<@{$member->id}> left the server.", "Left Server");
        });

//        Bot::getDiscord()->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord, ?Member $oldMember) {
//            $guild = Bot::get()->getGuild($member->guild_id);
//            $guild->log("<@{$member->id}> was updated.");
//        });
    }

}
