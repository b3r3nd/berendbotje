<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\User\Member;

interface GUILD_MEMBER_UPDATE
{
    /**
     * @param Member $member
     * @param Discord $discord
     * @param Member|null $oldMember
     * @return void
     */
    public function execute(Member $member, Discord $discord, ?Member $oldMember): void;
}
