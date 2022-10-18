<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use Discord\Http\Exceptions\NoPermissionsException;

class Say extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'say';
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
        $this->message->channel->sendMessage($this->messageString);
    }
}
