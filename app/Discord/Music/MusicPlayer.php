<?php

namespace App\Discord\Music;

use Illuminate\Support\Collection;

class MusicPlayer
{
    private bool $playing;
    private static ?MusicPlayer $musicPlayer;
    private Collection $queue;


    private function __construct()
    {
        $this->queue = collect([]);
        $this->playing = false;
    }

    public static function getPlayer(): MusicPlayer
    {
        if (!isset(self::$musicPlayer)) {
            self::$musicPlayer = new self();
        }
        return self::$musicPlayer;
    }

    public static function isPlaying(): bool
    {
        return self::getPlayer()->playing;
    }

    public function play(string $song): self
    {
        if ($this->playing) {
            $this->queue->push($song);
        }
        $this->playing = true;
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
        $this->queue = collect([]);
        return $this;
    }


    public function getQueue(): Collection
    {
        return $this->queue;
    }

    public function getStatus(): string
    {
        if (!$this->playing && $this->queue->isEmpty()) {
            return "Stopped";
        } else if (!$this->playing && !$this->queue->isEmpty()) {
            return "Paused";
        }
        return "Playing";
    }

}

