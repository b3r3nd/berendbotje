<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\Guild\Ban;

interface GUILD_BAN_REMOVE
{
    /**
     * @param Ban $ban
     * @param Discord $discord
     * @return void
     */
    public function execute(Ban $ban, Discord $discord): void;
}
