<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Guild;
use App\Discord\Core\MessageCreateEvent;
use App\Models\Channel;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

class React implements MessageCreateEvent
{
    /**
     * @throws NoPermissionsException
     */
    public function execute(Bot $bot, Guild $guild, Message $message, ?Channel $channel): void
    {
        if (!$guild->getSetting(Setting::ENABLE_REACTIONS)) {
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
    }
}
