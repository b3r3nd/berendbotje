<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate as DVoiceStateUpdate;
use Discord\WebSockets\Event;

class VoiceStateLogger
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::VOICE_STATE_UPDATE, function (DVoiceStateUpdate $state, Discord $discord, $oldstate) {
            $guild = Bot::get()->getGuild($state->guild_id ?? $oldstate->guild_id);

            if ($state->channel) {
                if (!isset($oldstate)) {
                    $guild->logWithMember($state->member, "<@{$state->member->id}> joined <#{$state->channel_id}>", 'success');
                }
            } else {
                $guild->logWithMember($oldstate->member, "<@{$oldstate->member->id}> left <#{$oldstate->channel_id}>", 'fail');
            }
        });
    }
}
