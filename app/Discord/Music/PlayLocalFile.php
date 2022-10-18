<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use Discord\Voice\VoiceClient;

class PlayLocalFile extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::USER;
    }

    public function trigger(): string
    {
        return 'play';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;
    }

    public function action(): void
    {
        foreach ($this->message->channel->guild->voice_states as $voiceState) {
            if ($voiceState->user_id === $this->message->author->id) {
                $channel = Bot::getDiscord()->getChannel($voiceState->channel_id);
                Bot::getDiscord()->joinVoiceChannel($channel)->then(function (VoiceClient $voice) {
                    $voice->playFile(public_path("veronica/{$this->arguments[0]}"))->done(function () use ($voice) {
                        $voice->close();
                    });
                });
            }
        }
    }
}
