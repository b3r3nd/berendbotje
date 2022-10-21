<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;

class Stop extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::USER;
    }

    public function trigger(): string
    {
        return 'stop';
    }

    public function action(): void
    {
        if(MusicPlayer::isPlaying()) {
            $this->message->channel->sendMessage(__('bot.music.stopping'));
            MusicPlayer::getPlayer()->stop();
        } else {
            $this->message->channel->sendMessage(__('bot.music.already-stopped'));
        }
    }
}
