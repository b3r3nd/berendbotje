<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Enums\Setting as SettingEnum;
use App\Discord\Moderation\Models\Abuser;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class Count extends DiscordEvent
{
    private string $lastCount = "";

    /**
     * @return void
     */
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot || !$message->guild_id) {
                return;
            }
            $guild = $this->bot->getGuild($message->guild_id);

            // We are actually possibly comparing integers with stings here.. soo no strict check.
            if ($message->channel_id != $guild->getSetting(SettingEnum::COUNT_CHANNEL) || !$guild->getSetting(SettingEnum::ENABLE_COUNT)) {
                return;
            }

            if (is_numeric($message->content)) {
                $guild = $this->bot->getGuild($message->guild_id);
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
        });
    }
}
