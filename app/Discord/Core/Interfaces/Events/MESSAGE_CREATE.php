<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\Channel\Message;

interface MESSAGE_CREATE
{
    /**
     * @param Message $message
     * @param Discord $discord
     * @return void
     */
    public function execute(Message $message, Discord $discord): void;
}
