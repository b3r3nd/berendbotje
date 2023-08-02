<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;

interface GUILD_CREATE
{
    /**
     * @param object $guild
     * @param Discord $discord
     * @return void
     */
    public function execute(object $guild, Discord $discord): void;
}
