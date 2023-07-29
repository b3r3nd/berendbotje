<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Guild;
use App\Discord\Core\MessageCreateEvent;
use App\Models\Channel;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

class CommandResponse implements MessageCreateEvent
{
    /**
     * @throws NoPermissionsException
     */
    public function execute(Bot $bot, Guild $guild, Message $message, ?Channel $channel): void
    {
        if (!$guild->getSetting(Setting::ENABLE_COMMANDS)) {
            return;
        }
        $guild->model->refresh();
        foreach ($guild->model->commands as $command) {
            if (strtolower($message->content) === strtolower($command->trigger)) {
                $message->channel->sendMessage($command->response);
            }
        }
    }
}
