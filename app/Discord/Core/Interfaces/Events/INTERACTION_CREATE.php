<?php

namespace App\Discord\Core\Interfaces\Events;

use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

interface INTERACTION_CREATE
{
    /**
     * @param Interaction $interaction
     * @param Discord $discord
     * @return void
     */
    public function execute(Interaction $interaction, Discord $discord): void;
}
