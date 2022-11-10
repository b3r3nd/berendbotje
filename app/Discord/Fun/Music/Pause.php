<?php

namespace App\Discord\Fun\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\MessageCommand;

class Pause extends MessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::USER;
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
