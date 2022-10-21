<?php

namespace App\Discord\Music;

use App\Discord\Core\Bot;
use App\Jobs\ProcessYoutubeDownload;
use App\Models\Song;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MusicPlayer
{
    private bool $playing;
    private static ?MusicPlayer $musicPlayer;
    private int $channelId;


    private function __construct()
    {
        $this->playing = false;
    }

    public static function getPlayer(): MusicPlayer
    {
        if (!isset(self::$musicPlayer)) {
            self::$musicPlayer = new self();
        }
        return self::$musicPlayer;
    }

    public function getChannel()
    {
        return $this->channelId;
    }

    public function setChannel(int $channelId): void
    {
        $this->channelId = $channelId;
    }

    public static function isPlaying(): bool
    {
        return self::getPlayer()->playing;
    }

    public function start(Message $message)
    {
        foreach ($message->channel->guild->voice_states as $voiceState) {
            if ($voiceState->user_id === $message->author->id) {
                $channel = Bot::getDiscord()->getChannel($voiceState->channel_id);

                $song = Song::orderBy('created_at')->first();

                $message->channel->sendMessage("Playing song ID {$song->id} with filename {$song->filename}");


                Bot::getDiscord()->joinVoiceChannel($channel)->done(function (VoiceClient $voice) use ($song, $message) {
                    $message->channel->sendMessage("Joined voice call, playing audio");
                    $voice->playFile(Storage::path($song->filename))->then(function () use ($song, $voice, $message) {
                        $message->channel->sendMessage("Finished playing audio, leaving voice call");
                        $voice->close();
                        $message->channel->sendMessage("Deleting audio from disk");
                        Storage::delete($song->filename);
                        $song->delete();
                    });
                });
            }
        }


    }

    public function play(string $song): self
    {
        ProcessYoutubeDownload::dispatch($song, MusicPlayer::getPlayer());
        return $this;
    }

    public function resume(): self
    {
        $this->playing = true;
        return $this;
    }

    public function pause(): self
    {
        $this->playing = false;
        return $this;
    }

    public function stop(): self
    {
        $this->playing = false;
        return $this;
    }


    public function getStatus(): string
    {
        if (!$this->playing) {
            return "Paused";
        }
        return "Playing";
    }

}

