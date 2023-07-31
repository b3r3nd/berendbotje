<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Moderation\Models\Channel;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

class MessageReact implements MessageCreateAction
{
    /**
     * @throws NoPermissionsException
     */
    public function execute(Bot $bot, Guild $guildModel, Message $message, ?Channel $channel): void
    {
        if (!$guildModel->getSetting(Setting::ENABLE_REACTIONS)) {
            return;
        }
        $guildModel->model->refresh();
        $msg = strtolower($message->content);

        foreach ($guildModel->model->reactions as $reaction) {
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
