<?php

namespace App\Discord\Core\Events;

use Discord\Discord;
use Discord\InteractionType;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;

class InteractionCreate extends DiscordEvent
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
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
        });
    }
}
