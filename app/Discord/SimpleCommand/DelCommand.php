<?php

namespace App\Discord\SimpleCommand;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use Discord\Http\Exceptions\NoPermissionsException;

class DelCommand extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'delcmd';
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
        \App\Models\Command::where(['trigger' => $this->arguments[0]])->delete();
        Bot::get()->deleteCommand($this->arguments[0]);
        $this->message->channel->sendMessage(__('bot.cmd.deleted'));
    }
}
