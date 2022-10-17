<?php

namespace App\Discord;

use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class CringeCounter
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }

            if (str_starts_with($message->content, "{$bot->getPrefix()}cringestats") ||
                str_starts_with($message->content, "{$bot->getPrefix()}cringecounter")) {
                $description = "";
                foreach (\App\Models\CringeCounter::orderBy('count', 'desc')->limit(10)->get() as $cringeCounter) {
                    $description .= "**{$cringeCounter->discord_username}** - {$cringeCounter->count} \n";
                }
                $embed = EmbedBuilder::create($discord,
                    __('bot.cringe.title'),
                    __('bot.cringe.footer'),
                    __('bot.cringe.description', ['users' => $description])
                );
                $message->channel->sendEmbed($embed);
                return;
            }

            if (str_starts_with($message->content, "{$bot->getPrefix()}cringe")) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
                }
                foreach ($message->mentions as $mention) {
                    $cringeCounter = \App\Models\CringeCounter::where(['discord_id' => $mention->id])->first();
                    if ($cringeCounter) {
                        $message->channel->sendMessage(__('bot.cringe.count', ['name' => $cringeCounter->discord_username, 'count' => $cringeCounter->count]));
                    } else {
                        $message->channel->sendMessage(__('bot.cringe.not-cringe', ['name' => $parameters[1]]));
                    }
                }
            }

            if (str_starts_with($message->content, "{$bot->getPrefix()}addcringe")) {
                if (!Admin::hasLevel($message->author->id, AccessLevels::USER->value)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
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
                        $message->channel->sendMessage(__('bot.cringe.change', ['name' => $cringeCounter->discord_username, 'count' => $cringeCounter->count]));
                    }
                }
            }

            if (str_starts_with($message->content, "{$bot->getPrefix()}delcringe")) {
                if (!Admin::hasLevel($message->author->id, AccessLevels::USER->value)) {
                    return;
                }
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
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
                            $message->channel->sendMessage(__('bot.cringe.not-cringe', ['name' => $parameters[1]]));
                        }
                        $message->channel->sendMessage((__('bot.cringe.change', ['name' => $cringeCounter->discord_username, 'count' => $cringeCounter->count])));
                    }
                }
            }
        });
    }

}
