<?php

namespace App\Discord\Core;

use Discord\Http\Exceptions\NoPermissionsException;

class Logger
{
    private string $logChannelId;

    /**
     * @param string $logChannelId
     */
    public function __construct(string $logChannelId)
    {
        $this->logChannelId = $logChannelId;
    }

    /**
     * @param string $message
     * @return void
     * @throws NoPermissionsException
     */
    public function log(string $message): void
    {
        Bot::getDiscord()->getChannel($this->logChannelId)->sendMessage($message);
    }

    /**
     * @param string $logChannelId
     * @return void
     */
    public function setLogChannelId(string $logChannelId): void
    {
        $this->logChannelId = $logChannelId;
    }
}
