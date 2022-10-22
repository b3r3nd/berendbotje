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
        $this->usageString = __('bot.music.usage-addsong');
    }

    public function action(): void
    {
        $musicPlayer = MusicPlayer::getPlayer();
        $musicPlayer->addToQueue($this->arguments[0]);
        $this->message->channel->sendMessage(__('bot.music.added'));
    }
}
