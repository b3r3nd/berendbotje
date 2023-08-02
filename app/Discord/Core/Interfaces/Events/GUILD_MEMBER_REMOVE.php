<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\User\Member;

interface GUILD_MEMBER_REMOVE
{
    /**
     * @param Member $member
     * @param Discord $discord
     * @return void
     */
    public function execute(Member $member, Discord $discord): void;
}
