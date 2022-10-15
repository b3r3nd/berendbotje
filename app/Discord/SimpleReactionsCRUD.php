<?php

namespace App\Discord;

use App\Models\Admin;
use App\Models\Command;
use App\Models\Reaction;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
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
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    Reaction::where(['trigger' => $parameters[1]])->delete();
                    $message->channel->sendMessage("Reaction deleted");
                }
            }

            if ($message->content == $bot->getPrefix() . 'reactions') {

                $embed = new Embed($discord);
                $embed->setType('rich');
                $embed->setFooter('usage: addcmd, delcmd, commands');
                $embed->setDescription('Basic text commands');
                $embed->setTitle("Commands");

                foreach (Reaction::all() as $reaction) {
                    $embed->addField(['name' => $reaction->trigger, 'value' => $reaction->reaction]);
                }
                $message->channel->sendEmbed($embed);

            }

        });
    }



}
