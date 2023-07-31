<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting as SettingEnum;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Models\Channel;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;

class MessageReminder implements MessageCreateAction
{

    public function execute(Bot $bot, Guild $guildModel, Message $message, ?Channel $channel): void
    {
        if (!$guildModel->getSetting(SettingEnum::ENABLE_REMINDER)) {
            return;
        }

        if ($message->channel_id == $guildModel->getSetting(SettingEnum::REMINDER_CHANNEL)) {
            $message->channel->sendMessage(MessageBuilder::new()->setContent("<@&{$guildModel->getSetting(SettingEnum::REMINDER_ROLE)}>"));
        }
    }
}
