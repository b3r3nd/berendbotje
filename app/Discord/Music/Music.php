<?php

namespace App\Discord\Music;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\WebSockets\VoiceStateUpdate;
use Discord\Repository\GuildRepository;
use Discord\Voice\VoiceClient;
use Discord\WebSockets\Event;

class Music
{
    public function __construct(Bot $bot)
    {

        $bot->discord()->on(Event::VOICE_STATE_UPDATE, function (VoiceStateUpdate $state, Discord $discord, $oldstate) {
            if ($state->channel) {
                foreach ($state->channel->guild->voice_states as $voiceState) {
                    if ($voiceState->user_id === $state->user_id) {
                        $voiceState->channel_id = $state->channel_id;
                    }
                }
            }
        });


        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if ((str_starts_with($message->content, "{$bot->getPrefix()}jingle"))) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
                }
                $voiceStates = $message->channel->guild->voice_states;
                $userId = $message->author->id;
                foreach ($voiceStates as $voiceState) {
                    if ($voiceState->user_id === $userId) {
                        $channel = $discord->getChannel($voiceState->channel_id);
                        $message->channel->sendMessage($voiceState->channel_id);
                        $message->channel->sendMessage($channel->name);
                        $discord->joinVoiceChannel($channel)->then(function (VoiceClient $voice) use ($parameters) {
                            return $voice->playFile(public_path("veronica/{$parameters[1]}.mp3"))->done(function () use ($voice) {
                                $voice->close();
                            });
                        });
                    }
                }
            }
        });
    }
}
