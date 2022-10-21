<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\Song;

class Play extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'play';
    }

    public function action(): void
    {
        if (MusicPlayer::isPlaying()) {
            $this->message->channel->sendMessage(__('bot.music.already-playing'));
        } else if (Song::count() == 0) {
            $this->message->channel->sendMessage(__('bot.music.no-music'));
        } else {
            $this->message->channel->sendMessage(__('bot.music.starting'));
            MusicPlayer::getPlayer()->start($this->message);
        }
    }
}
