<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\DiscordEvent;
use App\Discord\Settings\Enums\Setting as SettingEnum;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class Reminder extends DiscordEvent
{
    /**
     * @return void
     */
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot || !$message->guild_id ||
                $this->bot->getGuild($message->guild_id)?->getSetting(SettingEnum::ENABLE_REMINDER)) {
                return;
            }
            $guild = $this->bot->getGuild($message->guild_id);
            if ($message->channel_id === $guild->getSetting(SettingEnum::REMINDER_CHANNEL)) {
                $message->channel->sendMessage(MessageBuilder::new()->setContent("<@&{$guild->getSetting(SettingEnum::REMINDER_ROLE)}>"));
            }
        });
    }
}
