<?php

namespace App\Discord\Core\DiscordEvents;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Helper;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate as DVoiceStateUpdate;
use Discord\WebSockets\Event;

/**
 * For some reason the voice_states in the server is not updated after a user switches channel. It should update
 * when you add "Intents::GUILD_VOICE_STATES" to the initial call, yet it does not. So this simple
 * piece of code checks who switched channel and updates the voice_states accordingly.
 */
class VoiceStateUpdate
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::VOICE_STATE_UPDATE, function (DVoiceStateUpdate $state, Discord $discord, $oldstate) {
            $guild = Bot::get()->getGuild($state->guild_id ?? $oldstate->guild_id);
            $user = DiscordUser::get($state->user_id);

            if ($state->channel) {
                if ($state->self_deaf) {
                    if ($guild->isInvoice($state->user_id)) {
                        $this->leaveVoice($guild, $user, $state);
                    }
                } else {
                    if (!$guild->isInvoice($state->user_id)) {
                        $guild->joinedVoice($state->user_id);
                    }
                }

                // Update voice states because discord doesn't do it.
                foreach ($state->channel->guild->voice_states as $voiceState) {
                    if ($voiceState->user_id === $state->user_id) {
                        $voiceState->channel_id = $state->channel_id;
                    }
                }

            } else {
                if ($guild->isInvoice($state->user_id)) {
                    $this->leaveVoice($guild, $user, $state);
                }
            }
        });
    }

    /**
     * @param $guild
     * @param $user
     * @param $oldstate
     * @return void
     */
    private function leaveVoice($guild, $user, $oldstate): void
    {
        if ($guild->getSetting(Setting::ENABLE_VOICE_XP)) {
            $duration = $guild->leftVoice($oldstate->user_id);
            $xp = $guild->getSetting(Setting::VOICE_XP_COUNT);
            $cooldown = $guild->getSetting(Setting::XP_COOLDOWN);
            $amount = round(($duration / $cooldown) * $xp);

            $messageCounters = $user->messageCounters()->where('guild_id', $guild->model->id)->get();
            $messageCounter = new \App\Models\MessageCounter([
                'count' => 0,
                'guild_id' => $guild->model->id,
                'xp' => $amount,
                'voice_seconds' => $duration / 60,
            ]);

            if ($messageCounters->isEmpty()) {
                $user->messageCounters()->save($messageCounter);
            } else {
                $messageCounter = $messageCounters->first();
                $messageCounter->update([
                    'xp' => $messageCounter->xp + $amount,
                    'voice_seconds' => $messageCounter->voice_seconds + $duration,
                ]);
            }
            $messageCounter->update(['level' => Helper::calcLevel($messageCounter->xp)]);
        }
    }
}
