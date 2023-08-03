<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\Channel\Invite;

interface INVITE_CREATE
{
    /**
     * @param Invite $invite
     * @param Discord $discord
     * @return void
     */
    public function execute(Invite $invite, Discord $discord): void;
}
