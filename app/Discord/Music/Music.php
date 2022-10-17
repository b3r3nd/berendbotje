<?php

namespace App\Discord\Music;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use Discord\WebSockets\Event;

class Music
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if ((str_starts_with($message->content, "{$bot->getPrefix()}jingle"))) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
                }

                $channel = $discord->getChannel('1031629548527300649');

                $discord->joinVoiceChannel($channel)->then(function (VoiceClient $voice) use ($parameters) {
                    return $voice->playFile(public_path("veronica/{$parameters[1]}.mp3"));
                })->done(function () {
                    var_dump('done');
                });
            }

        });
    }
}
