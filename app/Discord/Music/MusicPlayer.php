<?php

namespace App\Discord\Music;

use App\Discord\Core\Bot;
use App\Jobs\ProcessYoutubeDownload;
use App\Models\Song;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use Illuminate\Support\Facades\Storage;

class MusicPlayer
{
    private bool $playing;
    private static ?MusicPlayer $musicPlayer;

    private function __construct()
    {
        $this->playing = false;
    }

    /**
     * @return MusicPlayer
     */
    public static function getPlayer(): MusicPlayer
    {
        if (!isset(self::$musicPlayer)) {
            self::$musicPlayer = new self();
        }
        return self::$musicPlayer;
    }

    /**
     * @param string $song
     * @return $this
     */
    public function addToQueue(string $song): self
    {
        ProcessYoutubeDownload::dispatch($song, $this);
        return $this;
    }

    /**
     * @param Message $message
     * @return void
     */
    public function start(Message $message): void
    {
        foreach ($message->channel->guild->voice_states as $voiceState) {
            if ($voiceState->user_id === $message->author->id) {
                Bot::getDiscord()->joinVoiceChannel(Bot::getDiscord()->getChannel($voiceState->channel_id))->done(function (VoiceClient $voice) use ($message) {
                    $message->channel->sendMessage("Joined voice call, playing audio");
                    $song = Song::orderBy('created_at')->first();
                    $this->playFile($song, $voice, $message);
                });
            }
        }
    }

    /**
     * @param $song
     * @param $voice
     * @param $message
     * @return void
     */
    private function playFile($song, $voice, $message): void
    {
        $voice->playFile(Storage::path($song->filename))->then(function () use ($song, $voice, $message) {
            Storage::delete($song->filename);
            $song->delete();
            $message->channel->sendMessage("Playing next song");
            $song = Song::orderBy('created_at')->first();
            if ($song) {
                $this->playFile($song, $voice, $message);
            } else {
                $voice->close();
            }
        });
    }

    /**
     * @return bool
     */
    public static function isPlaying(): bool
    {
        return self::getPlayer()->playing;
    }

    /**
     * @return $this
     */
    public function resume(): self
    {
        $this->playing = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function pause(): self
    {
        $this->playing = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function stop(): self
    {
        $this->playing = false;
        return $this;
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        if (!$this->playing) {
            return "Paused";
        }
        return "Playing";
    }

}

