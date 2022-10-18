<?php

namespace App\Discord\SimpleReaction;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Models\Reaction;
use Discord\Http\Exceptions\NoPermissionsException;

class DelReaction extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'delreaction';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        Reaction::where(['trigger' => $this->arguments[0]])->delete();
        Bot::get()->deleteReaction($this->arguments[0]);
        $this->message->channel->sendMessage(__('bot.reactions.deleted'));
    }
}
