<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\Events\DiscordEvent;
use App\Discord\Logger\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate as DVoiceStateUpdate;
use Discord\WebSockets\Event;
use Exception;

class VoiceStateLogger extends DiscordEvent
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
     * @throws Exception
     */
    public function execute(DVoiceStateUpdate $state, Discord $discord, $oldstate): void
    {
        $guild = $this->bot->getGuild($state->guild_id ?? $oldstate->guild_id);
        if ($state->channel) {
            if (!isset($oldstate)) {
                if ($guild->getLogSetting(LogSetting::JOINED_CALL)) {
                    $guild->logWithMember($state->member, __('bot.log.joined-call', ['user' => $state->member->id, 'channel' => $state->channel_id]), 'success');
                }
            } else if ($state->deaf || $state->mute) {
                if ($guild->getLogSetting(LogSetting::MUTED_MOD_VOICE)) {
                    $guild->logWithMember($state->member, __('bot.log.muted-call', ['user' => $state->member->id]), 'fail');
                }
            } else if ($state->deaf !== $oldstate->deaf || $state->mute !== $oldstate->mute) {
                if ($guild->getLogSetting(LogSetting::UNMUTED_MOD_VOiCE)) {
                    $guild->logWithMember($state->member, __('bot.log.unmuted-call', ['user' => $state->member->id]), 'success');
                }
            } else if ($state->self_stream && !$oldstate->self_stream) {
                if ($guild->getLogSetting(LogSetting::START_STREAM)) {
                    $guild->logWithMember($state->member, __('bot.log.start-stream', ['user' => $state->member->id, 'channel' => $state->channel_id]), 'success');
                }
            } else if ($state->self_video && !$oldstate->self_video) {
                if ($guild->getLogSetting(LogSetting::START_CAM)) {
                    $guild->logWithMember($state->member, __('bot.log.enable-cam', ['user' => $state->member->id, 'channel' => $state->channel_id]), 'success');
                }
            } else if (!$state->self_stream && $oldstate->self_stream) {
                if ($guild->getLogSetting(LogSetting::END_STREAM)) {
                    $guild->logWithMember($state->member, __('bot.log.stop-stream', ['user' => $state->member->id, 'channel' => $state->channel_id]), 'fail');
                }
            } else if (!$state->self_video && $oldstate->self_video) {
                if ($guild->getLogSetting(LogSetting::END_CAM)) {
                    $guild->logWithMember($state->member, __('bot.log.disable-cam', ['user' => $state->member->id, 'channel' => $state->channel_id]), 'fail');
                }
            } else if ($state->self_deaf === $oldstate->self_deaf && $state->self_mute === $oldstate->self_mute && $guild->getLogSetting(LogSetting::SWITCHED_CALL)) {
                $guild->logWithMember($state->member, __('bot.log.switch-call', ['user' => $state->member->id, 'oldchannel' => $oldstate->channel_id, 'newchannel' => $state->channel_id]), 'success');
            }
        } else if ($guild->getLogSetting(LogSetting::LEFT_CALL)) {
            $guild->logWithMember($oldstate->member, __('bot.log.left-call', ['user' => $state->member->id, 'channel' => $oldstate->channel_id]), 'fail');
        }
    }
}
