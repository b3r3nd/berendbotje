<?php

namespace App\Discord\CustomCommands\Events;

use App\Discord\Core\DiscordEvent;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class CommandResponse extends DiscordEvent
{
    /**
     * @return void
     */
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            $guild = $this->bot->getGuild($message->guild_id);
            if ($message->author->bot || !$guild->getSetting(\App\Discord\Settings\Enums\Setting::ENABLE_COMMANDS)) {
                return;
            }
            $guild->model->refresh();
            foreach ($guild->model->commands as $command) {
                if (strtolower($message->content) === strtolower($command->trigger)) {
                    $message->channel->sendMessage($command->response);
                }
            }
        });
    }
}
