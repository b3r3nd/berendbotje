<?php

namespace App\Discord\Fun;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting as SettingEnum;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class QuestionOfTheDayReminder
{
    protected Bot $bot;
    protected Discord $discord;

    public function __construct(Bot $bot, string $guildId)
    {
        $this->bot = $bot;
        $this->discord = $bot->discord;

        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($guildId) {
            if ($message->author->bot || !$message->guild_id || $message->guild_id !== $guildId ||
                $this->bot->getGuild($$guildId)?->getSetting(SettingEnum::ENABLE_QOTD_REMINDER)) {
                return;
            }
            $guild = $this->bot->getGuild($guildId);
            if ($message->channel_id == $guild->getSetting(SettingEnum::QOTD_CHANNEL)) {
                $message->channel->sendMessage(MessageBuilder::new()->setContent("<@&{$guild->getSetting(SettingEnum::QOTD_ROLE)}>"));
            }
        });
    }
}
