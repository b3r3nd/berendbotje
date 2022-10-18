<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;

class Pause extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'pause';
    }

    public function action(): void
    {
        if (MusicPlayer::isPlaying()) {
            $this->message->channel->sendMessage(__('bot.music.pausing'));
            MusicPlayer::getPlayer()->pause();
        } else {
            $this->message->channel->sendMessage(__('bot.music.already-paused'));
        }
    }
}
