<?php

namespace App\Discord\Moderation\Events;

use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Events\DiscordEvent;
use App\Discord\Moderation\Models\CustomMessage;
use Discord\Discord;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;

class WelcomeUser extends DiscordEvent
{
    public function event(): string
    {
        return Event::GUILD_MEMBER_ADD;
    }

    /**
     * @param Member $member
     * @param Discord $discord
     * @return void
     * @throws NoPermissionsException
     */
    public function execute(Member $member, Discord $discord): void
    {
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
    }
}