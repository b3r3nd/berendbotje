<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting as SettingEnum;
use App\Discord\Core\Guild;
use App\Discord\Core\MessageCreateEvent;
use App\Discord\Moderation\Models\Abuser;
use App\Models\Channel;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;

class Count implements MessageCreateEvent
{
    private string $lastCount = "";

    public function execute(Bot $bot, Guild $guild, Message $message, ?Channel $channel): void
    {
        if ($message->channel_id != $guild->getSetting(SettingEnum::COUNT_CHANNEL) || !$guild->getSetting(SettingEnum::ENABLE_COUNT)) {
            return;
        }

        if (is_numeric($message->content)) {
            $number = (int)$message->content;
            $count = $guild->getSetting(SettingEnum::CURRENT_COUNT);
            $newCount = $count + 1;


            if (!(Abuser::where('discord_id', $message->author->id)->get())->isEmpty()) {
                $message->delete();
                return;
            }

            if ($this->lastCount === $message->author->id) {
                $message->react("❌");
                return;
            }

            // 42 is always the right answer...
            if ($number === 42 && $newCount !== 42) {
                $message->react("🧠");
                return;
            }

            if ($number !== $newCount) {
                $count = 0;
                $this->lastCount = "";
                $guild->setSetting(SettingEnum::CURRENT_COUNT->value, $count);
                $message->react("❌");
                Abuser::create(['discord_id' => $message->author->id, 'guild_id' => $message->guild_id, 'reason' => __('bot.cannot-count')]);
                $message->channel->sendMessage(MessageBuilder::new()->setContent(__('bot.wrong-number', ['count' => $count])));
                return;
            }

            $this->lastCount = $message->author->id;
            $count++;
            $guild->setSetting(SettingEnum::CURRENT_COUNT->value, $count);

            $message->react("✅");
        } else {
            $message->delete();
        }
    }
}
