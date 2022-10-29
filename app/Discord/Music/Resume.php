<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\MessageCommand;

class Resume extends MessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::USER;
    }

    public function trigger(): string
    {
        return 'resume';
    }

    public function action(): void
    {
        if (!MusicPlayer::isPlaying()) {
            $this->message->channel->sendMessage(__('bot.music.resuming'));
        } else {
            $this->message->channel->sendMessage(__('bot.music.already-playing'));
        }
        MusicPlayer::getPlayer()->resume();
    }
}
