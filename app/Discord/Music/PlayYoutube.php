<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use Discord\Voice\VoiceClient;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class PlayYoutube extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'yt';
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

                $filename = 'tmp.mp3';
                $tmpFileLocation = Storage::path($filename);

                $this->message->channel->sendMessage("Downloading audio as {$tmpFileLocation}...");
                system("youtube-dl -x -o {$tmpFileLocation} --audio-format mp3 {$this->arguments[0]}");

                $this->message->channel->sendMessage("Audio stored on hard disk, joining voice call");
                Bot::getDiscord()->joinVoiceChannel($channel)->then(function (VoiceClient $voice) use ($tmpFileLocation, $filename) {

                    $this->message->channel->sendMessage("Joined voice call, playing audio");
                    $voice->playFile($tmpFileLocation)->done(function () use ($voice, $filename) {

                        $this->message->channel->sendMessage("Finished playing audo, leaving voice call");
                        $voice->close();

                        $this->message->channel->sendMessage("Deleting audio from disk");
                        Storage::delete($filename);

                        $this->message->channel->sendMessage("Done! Ezpz");
                    });
                });
            }
        }
    }
}
