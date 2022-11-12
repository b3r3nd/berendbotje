<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\LogSetting;
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
                    if ($guild->getLogSetting(LogSetting::JOINED_CALL)) {
                        $guild->logWithMember($state->member, "<@{$state->member->id}> joined <#{$state->channel_id}>", 'success');
                    }
                } else {
                    if ($state->deaf || $state->mute) {
                        if ($guild->getLogSetting(LogSetting::MUTED_MOD_VOICE)) {
                            $guild->logWithMember($state->member, "<@{$state->member->id}> was muted in voice", 'fail');
                        }
                    } else if ($state->deaf != $oldstate->deaf || $state->mute != $oldstate->mute) {
                        if ($guild->getLogSetting(LogSetting::UNMUTED_MOD_VOiCE)) {
                            $guild->logWithMember($state->member, "<@{$state->member->id}> was unmuted in voice", 'success');
                        }
                    } else if ($state->self_stream && !$oldstate->self_stream) {
                        if ($guild->getLogSetting(LogSetting::START_STREAM)) {
                            $guild->logWithMember($state->member, "<@{$state->member->id}> started streaming in <#{$state->channel_id}>", 'success');
                        }

                    } else if ($state->self_video && !$oldstate->self_video) {
                        if ($guild->getLogSetting(LogSetting::START_CAM)) {
                            $guild->logWithMember($state->member, "<@{$state->member->id}> enabled his webcam in <#{$state->channel_id}>", 'success');
                        }

                    } else if (!$state->self_stream && $oldstate->self_stream) {
                        if ($guild->getLogSetting(LogSetting::END_STREAM)) {
                            $guild->logWithMember($state->member, "<@{$state->member->id}> stopped streaming in <#{$state->channel_id}>", 'fail');
                        }

                    } else if (!$state->self_video && $oldstate->self_video) {
                        if ($guild->getLogSetting(LogSetting::END_CAM)) {
                            $guild->logWithMember($state->member, "<@{$state->member->id}> disabled his webcam in <#{$state->channel_id}>", 'fail');
                        }

                    } else if ($state->self_deaf == $oldstate->self_deaf && $state->self_mute == $oldstate->self_mute) {
                        if ($guild->getLogSetting(LogSetting::SWITCHED_CALL)) {
                            $guild->logWithMember($state->member, "<@{$state->member->id}> switched from <#{$oldstate->channel_id}> to <#{$state->channel_id}>", 'success');
                        }
                    }
                }
            } else {
                if ($guild->getLogSetting(LogSetting::LEFT_CALL)) {
                    $guild->logWithMember($oldstate->member, "<@{$oldstate->member->id}> left <#{$oldstate->channel_id}>", 'fail');
                }
            }
        });
    }


}
