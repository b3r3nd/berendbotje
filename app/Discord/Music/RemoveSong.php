<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\Song;

class RemoveSong extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'remove';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;
        $this->usageString = __('bot.music.usage-remove');
    }

    public function action(): void
    {
        $song = Song::find($this->arguments[0]);

        if ($song) {
            $song->delete();
            $this->message->channel->sendMessage(__('bot.music.removed-song'));
        } else {
            $this->message->channel->sendMessage(__('bot.music.not-found'));
        }
    }
}
