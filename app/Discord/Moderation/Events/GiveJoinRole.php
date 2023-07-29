<?php

namespace App\Discord\Moderation\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Enums\Setting;
use App\Discord\CustomMessages\Models\CustomMessage;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class GiveJoinRole extends DiscordEvent
{
    /**
     * @return void
     */
    public function registerEvent(): void
    {
        $this->discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
            $guild = $this->bot->getGuild($member->guild_id);
            if ($guild?->getSetting(Setting::ENABLE_JOIN_ROLE)) {
                $member->addRole($guild->getSetting(Setting::JOIN_ROLE));
            }
            if ($guild?->getSetting(Setting::ENABLE_WELCOME_MSG)) {
                $welcomeMessage = CustomMessage::welcome($member->guild_id)->inRandomOrder()->first();
                if ($welcomeMessage) {
                    $discord->getChannel($guild->getSetting(Setting::WELCOME_MSG_CHAN))?->sendMessage(str_replace(':user', "<@{$member->id}>", $welcomeMessage->message));
                }
            }
        });
    }
}
