<?php

namespace App\Discord\Core\Events;

use Discord\Discord;
use Discord\InteractionType;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;

class InteractionCreate extends DiscordEvent
{
    public function event(): string
    {
        return Event::INTERACTION_CREATE;
    }

    /**
     * @param Interaction $interaction
     * @param Discord $discord
     * @return void
     */
    public function execute(Interaction $interaction, Discord $discord): void
    {
        $option = $interaction->data->options?->first();
        $trigger = "{$interaction->data->name}_{$option?->name}";
        if (!isset($this->bot->commands[$trigger]) && $option?->options->first()) {
            $trigger = "{$option?->name}_{$option->options->first()?->name}";
        }
        if (isset($this->bot->commands[$trigger])) {
            if ($interaction->type === InteractionType::APPLICATION_COMMAND) {
                $this->bot->commands[$trigger]->execute($interaction);
            }
            if ($interaction->type === InteractionType::APPLICATION_COMMAND_AUTOCOMPLETE) {
                $this->bot->commands[$trigger]->complete($interaction);
            }
        }

    }
}
