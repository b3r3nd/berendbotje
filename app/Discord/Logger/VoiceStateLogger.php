<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate as DVoiceStateUpdate;
use Discord\WebSockets\Event;

class VoiceStateLogger
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::VOICE_STATE_UPDATE, function (DVoiceStateUpdate $state, Discord $discord, $oldstate) {
            $guild = Bot::get()->getGuild($state->guild_id ?? $oldstate->guild_id);
            $user = DiscordUser::get($state->user_id);

            if ($state->channel) {
                $guild->log("{$user->tag()} has joined <#{$state->channel_id}>", "Joined voice call", 'success');
            } else {
                $guild->log("{$user->tag()} has left <#{$oldstate->channel_id}>", "Left voice call", 'fail');
            }
        });
    }
}
