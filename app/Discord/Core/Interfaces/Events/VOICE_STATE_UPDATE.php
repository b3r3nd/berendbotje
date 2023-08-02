<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\WebSockets\VoiceStateUpdate as DVoiceStateUpdate;

interface VOICE_STATE_UPDATE
{
    /**
     * @param DVoiceStateUpdate $state
     * @param Discord $discord
     * @param $oldstate
     * @return void
     */
    public function execute(DVoiceStateUpdate $state, Discord $discord, $oldstate): void;
}
