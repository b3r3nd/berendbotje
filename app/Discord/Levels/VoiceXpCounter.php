<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Helper;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate as DVoiceStateUpdate;
use Discord\WebSockets\Event;

class VoiceXpCounter
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
                'voice_seconds' => $duration,
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