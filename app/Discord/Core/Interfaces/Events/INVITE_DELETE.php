<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;

interface INVITE_DELETE
{
    /**
     * @param object $invite
     * @param Discord $discord
     * @return void
     */
    public function execute(object $invite, Discord $discord): void;
}
