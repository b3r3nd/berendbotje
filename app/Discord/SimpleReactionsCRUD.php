<?php

namespace App\Discord;

use App\Models\Admin;
use App\Models\Command;
use App\Models\Reaction;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleReactionsCRUD
{
    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if (!Admin::hasLevel($message->author->id, AccessLevels::MOD->value)) {
                return;
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'addreaction ')) {
                if (!Admin::isAdmin($message->author->id)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1]) || !isset($parameters[2])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    $message->channel->sendMessage("Reaction saved");
                    $command = Reaction::create(['trigger' => $parameters[1], 'reaction' => $parameters[2]]);
                    $command->save();
                    new SimpleReaction($discord, $parameters[1], $parameters[2]);
                }
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'delreaction ')) {
                if (!Admin::isAdmin($message->author->id)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    Reaction::where(['trigger' => $parameters[1]])->delete();
                    $message->channel->sendMessage("Reaction deleted");
                }
            }

            if ($message->content == $bot->getPrefix() . 'reactions') {
                if (!Admin::isAdmin($message->author->id)) {
                    return;
                }
                $reactions = '';
                foreach (Reaction::all() as $reaction) {
                    $reactions .= $reaction->trigger . ' ';
                }
                $message->channel->sendMessage($reactions);
            }

        });
    }



}
