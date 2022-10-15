<?php

namespace App\Discord;

use App\Models\Command;
use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;

class SimpleCommandCRUD
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
            if (str_starts_with($message->content, $bot->getPrefix() . 'addcmd ')) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1]) || !isset($parameters[2])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    $message->channel->sendMessage("Command saved");
                    $command = Command::create(['trigger' => $parameters[1], 'response' => $parameters[2]]);
                    $command->save();
                    new SimpleCommand($discord, $parameters[1], $parameters[2]);
                }
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'delcmd ')) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    Command::where(['trigger' => $parameters[1]])->delete();
                    $message->channel->sendMessage("Command deleted");
                }

            }
            if ($message->content == $bot->getPrefix() . 'commands') {
                $embed = new Embed($discord);
                $embed->setType('rich');
                $embed->setFooter('usage: addcmd, delcmd, commands');
                $embed->setDescription('Basic text commands');
                $embed->setTitle("Commands");

                foreach (Command::all() as $command) {
                    $embed->addField(['name' => $command->trigger, 'value' => $command->response]);
                }
                $message->channel->sendEmbed($embed);
            }
        });
    }
}
