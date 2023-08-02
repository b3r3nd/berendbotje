<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;

interface MESSAGE_DELETE
{
    /**
     * @param object $message
     * @param Discord $discord
     * @return void
     */
    public function execute(object $message, Discord $discord): void;
}
