<?php

namespace App\Discord\Music;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\VoiceStateUpdate;
use Discord\Repository\GuildRepository;
use Discord\Voice\VoiceClient;
use Discord\WebSockets\Event;

class Music
{
    public function __construct(Bot $bot)
    {

        $bot->discord()->on(Event::VOICE_STATE_UPDATE, function (VoiceStateUpdate $state, Discord $discord, $oldstate) {

            var_dump("switch");
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


//                $discord->guilds->freshen()->done(function (GuildRepository $guilds) use ($discord, $message) {
//                    $guild = $guilds->get('id', 590941503917129743);
//
//                    var_dump($guild->name);
//
//                    var_dump($guild->voice_states);
//
//                    foreach ($guild->voice_states as $voiceState) {
//                        var_dump('test');
//                        if ($voiceState->user_id === $message->author->id) {
//                            $channel = $discord->getChannel($voiceState->channel_id);
//                            $message->channel->sendMessage($voiceState->channel_id);
//                            $message->channel->sendMessage($channel->name);
//                        }
//                    }
//                });

                foreach ($message->channel->guild->voice_states as $voiceState) { //Find a voice channel the user is in
                    if ($voiceState->user_id === $message->author->id) {
                        $channel = $discord->getChannel($voiceState->channel_id);
                        $message->channel->sendMessage($voiceState->channel_id);
                        $message->channel->sendMessage($channel->name);
                    }
                }

                $discord->joinVoiceChannel($channel)->then(function (VoiceClient $voice) use ($parameters) {
                    return $voice->playFile(public_path("veronica/{$parameters[1]}.mp3"))->done(function () use ($voice) {
                        $voice->close();
                    });
                });
            }

        });
    }
}
