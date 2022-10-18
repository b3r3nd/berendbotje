<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;

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

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;

    }

    public function action(): void
    {
        if (MusicPlayer::isPlaying()) {
            $this->message->channel->sendMessage(__('bot.music.add-to-queue'));
        } else {
            $this->message->channel->sendMessage(__('bot.music.starting'));
        }

        MusicPlayer::getPlayer()->play($this->arguments[0]);
    }
}
