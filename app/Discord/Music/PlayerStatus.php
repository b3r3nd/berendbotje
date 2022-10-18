<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;

class PlayerStatus extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'status';
    }

    public function action(): void
    {
        $this->message->channel->sendMessage(__('bot.music.player-status', ['status' => MusicPlayer::getPlayer()->getStatus()]));
    }
}
