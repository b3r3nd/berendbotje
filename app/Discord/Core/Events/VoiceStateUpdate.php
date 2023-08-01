<?php

namespace App\Discord\Core\Events;

use App\Discord\Core\DiscordEvent;
use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate as DVoiceStateUpdate;
use Discord\WebSockets\Event;

/**
 * For some reason the voice_states in the server is not updated after a user switches channel. It should update
 * when you add "Intents::GUILD_VOICE_STATES" to the initial call, yet it does not. So this simple
 * piece of code checks who switched channel and updates the voice_states accordingly.
 *
 * Probably, I am doing something wrong... but hey this works for now...
 */
class VoiceStateUpdate extends DiscordEvent
{
    public function event(): string
    {
        return Event::VOICE_STATE_UPDATE;
    }

    /**
     * @param DVoiceStateUpdate $state
     * @param Discord $discord
     * @param $oldstate
     * @return void
     */
    public function execute(DVoiceStateUpdate $state, Discord $discord, $oldstate): void
    {
        if ($state->channel) {
            if (is_array($state->channel->guild->voice_states)) {
                foreach ($state->channel->guild->voice_states as $voiceState) {
                    if ($voiceState->user_id === $state->user_id) {
                        $voiceState->channel_id = $state->channel_id;
                    }
                }
            }
        }
    }
}
