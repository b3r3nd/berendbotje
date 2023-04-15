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
    private string $guildId;
    private int $guildModelId;

    public function __construct(string $guildId)
    {
        $this->guildId = $guildId;
        $this->guildModelId = \App\Models\Guild::get($guildId)->id;
        $this->register();
    }


    public function register()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot || !$message->guild_id || $message->guild_id !== $this->guildId ||
                !Bot::get()->getGuild($this->guildId)?->getSetting(SettingEnum::ENABLE_QOTD_REMINDER)) {
                return;
            }

            $guild = Bot::get()->getGuild($this->guildId);
            if ($message->channel_id == $guild->getSetting(SettingEnum::QOTD_CHANNEL)) {
                $message->channel->sendMessage(MessageBuilder::new()->setContent("<@&{$guild->getSetting(SettingEnum::QOTD_ROLE)}>"));
            }

        });
    }
}
