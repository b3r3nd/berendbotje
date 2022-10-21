<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;

class AddSong extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'addsong';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;

    }

    public function action(): void
    {
        $musicPlayer = MusicPlayer::getPlayer();
        $musicPlayer->setChannel($this->message->channel_id);
        $musicPlayer->play($this->arguments[0]);
    }
}
