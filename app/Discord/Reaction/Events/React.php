<?php

namespace App\Discord\Reaction\Events;

use App\Discord\Core\DiscordEvent;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class React extends DiscordEvent
{
    /**
     * @return void
     */
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            $guild = $this->bot->getGuild($message->guild_id);

            if ($message->author->bot || !$guild->getSetting(\App\Discord\Settings\Enums\Setting::ENABLE_REACTIONS)) {
                return;
            }
            $guild->model->refresh();
            $msg = strtolower($message->content);

            foreach ($guild->model->reactions as $reaction) {
                preg_match("/\b{$reaction->trigger}\b|^{$reaction->trigger}\b|\b{$reaction->trigger}$/", $msg, $result);

                if (!empty($result)) {
                    if (str_contains($reaction->reaction, "<")) {
                        $message->react(str_replace(["<", ">"], "", $reaction->reaction));
                    } else {
                        $message->react($reaction->reaction);
                    }
                }
            }
        });
    }
}
