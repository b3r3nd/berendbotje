<?php

namespace App\Discord\Fun\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\MessageCommand;
use App\Models\Song;
use Illuminate\Support\Facades\Storage;

class RemoveSong extends MessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'removesong';
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
            Storage::delete($song->filename);
            $song->delete();
            $this->message->channel->sendMessage(__('bot.music.removed-song'));
        } else {
            $this->message->channel->sendMessage(__('bot.music.not-found'));
        }
    }
}
