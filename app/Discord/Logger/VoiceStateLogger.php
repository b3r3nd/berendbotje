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
                } else {
                    if ($state->deaf || $state->mute) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> was muted in voice", 'fail');
                    } else if ($state->deaf != $oldstate->deaf || $state->mute != $oldstate->mute) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> was unmuted in voice", 'success');


                    } else if ($state->self_stream && !$oldstate->self_stream) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> started streaming in <#{$state->channel_id}>", 'success');

                    } else if ($state->self_video && !$oldstate->self_video) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> enabled his webcam in <#{$state->channel_id}>", 'success');

                    } else if (!$state->self_stream && $oldstate->self_stream) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> stopped streaming in <#{$state->channel_id}>", 'fail');

                    } else if (!$state->self_video && $oldstate->self_video) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> disabled his webcam in <#{$state->channel_id}>", 'fail');

                    } else if ($state->self_deaf == $oldstate->self_deaf && $state->self_mute == $oldstate->self_mute) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> switched from <#{$oldstate->channel_id}> to <#{$state->channel_id}>", 'success');
                    }
                }
            } else {
                $guild->logWithMember($oldstate->member, "<@{$oldstate->member->id}> left <#{$oldstate->channel_id}>", 'fail');
            }
        });
    }


}
