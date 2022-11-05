<?php

namespace App\Discord\Fun;

use App\Discord\Core\Command\MessageCommand;
use Discord\Http\Exceptions\NoPermissionsException;

class Say extends MessageCommand
{
    public function permission(): string
    {
        return "";
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
