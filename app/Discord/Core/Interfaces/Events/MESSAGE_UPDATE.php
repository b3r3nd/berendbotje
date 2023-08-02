<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\Channel\Message;

interface MESSAGE_UPDATE
{
    /**
     * @param $message
     * @param Discord $discord
     * @param Message|null $oldMessage
     * @return void
     */
    public function execute($message, Discord $discord, ?Message $oldMessage): void;
}
