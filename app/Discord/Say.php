<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\MessageCommand;
use Discord\Http\Exceptions\NoPermissionsException;

class Say extends MessageCommand
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
        $this->usageString = __('bot.say-usage');

    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        $this->message->channel->sendMessage($this->messageString);
    }
}
