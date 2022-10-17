<?php

namespace App\Discord;

use App\Models\Admin;
use App\Models\Bumper;
use App\Models\Reaction;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;

class CringeCounter
{
    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'cringestats') || str_starts_with($message->content, $bot->getPrefix() . 'cringecounter')) {
                $embed = new Embed($discord);
                $embed->setType('rich');
                $embed->setFooter('usage: addcringe');
                $embed->setDescription('Most cringe people in our discord');
                $embed->setTitle('Cringe Counter');
                $embed->setColor(2067276);
                foreach (\App\Models\CringeCounter::orderBy('count', 'desc')->limit(10)->get() as $cringeCounter) {
                    $embed->addField(['name' => $cringeCounter->discord_username, 'value' => $cringeCounter->count]);
                }
                $message->channel->sendEmbed($embed);
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'cringe ')) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage("Provide arguments noob");
                }
                foreach ($message->mentions as $mention) {
                    $cringeCounter = \App\Models\CringeCounter::where(['discord_id' => $mention->id])->first();

                    if ($cringeCounter) {
                        $message->channel->sendMessage("Cringe counter for " . $cringeCounter->discord_username . " is " . $cringeCounter->count);
                    } else {
                        $message->channel->sendMessage($parameters[1] . " isn't cringe..");
                    }
                }

            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'addcringe ')) {
                if (!Admin::hasLevel($message->author->id, AccessLevels::USER->value)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    foreach ($message->mentions as $mention) {
                        $cringeCounter = \App\Models\CringeCounter::where(['discord_id' => $mention->id])->first();

                        if ($cringeCounter) {
                            $cringeCounter->count = $cringeCounter->count + 1;
                            $cringeCounter->save();
                        } else {
                            $cringeCounter = \App\Models\CringeCounter::create([
                                'discord_tag' => $mention,
                                'discord_id' => $mention->id,
                                'discord_username' => $mention->username,
                                'count' => 1
                            ]);
                        }
                        $message->channel->sendMessage("Cringe counter for " . $cringeCounter->discord_tag . " increased to " . $cringeCounter->count);
                    }
                }
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'delcringe ')) {
                if (!Admin::hasLevel($message->author->id, AccessLevels::USER->value)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage("Provide arguments noob");
                } else {
                    foreach ($message->mentions as $mention) {
                        $cringeCounter = \App\Models\CringeCounter::where(['discord_id' => $mention->id])->first();

                        if ($cringeCounter) {
                            $cringeCounter->count = $cringeCounter->count - 1;
                            if ($cringeCounter->count == 0) {
                                $cringeCounter->delete();
                            } else {
                                $cringeCounter->save();
                            }
                        } else {
                            $message->channel->sendMessage($parameters[1] . " isn't cringe..");
                        }
                        $message->channel->sendMessage("Cringe counter for " . $cringeCounter->discord_tag . " decreased to " . $cringeCounter->count);
                    }
                }
            }
        });
    }

}
