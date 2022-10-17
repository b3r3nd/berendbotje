<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;
use App\Models\Reaction;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleReactionsCRUD
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if (!Admin::hasLevel($message->author->id, AccessLevels::MOD->value)) {
                return;
            }
            if (str_starts_with($message->content, "{$bot->getPrefix()}addreaction")) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1]) || !isset($parameters[2])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
                } else {
                    $message->channel->sendMessage(__('bot.reactions.saved'));
                    $command = Reaction::create(['trigger' => $parameters[1], 'reaction' => $parameters[2]]);
                    $command->save();
                    new SimpleReaction($bot, $parameters[1], $parameters[2]);
                }
            }
            if (str_starts_with($message->content, "{$bot->getPrefix()}delreaction")) {
                $parameters = explode(' ', $message->content);
                if (!isset($parameters[1])) {
                    $message->channel->sendMessage(__('bot.provide-args'));
                } else {
                    Reaction::where(['trigger' => $parameters[1]])->delete();
                    $message->channel->sendMessage(__('bot.reactions.deleted'));
                }
            }
            if ($message->content == "{$bot->getPrefix()}reactions") {
                $embed = EmbedBuilder::create($discord,
                    __('bot.reactions.title'),
                    __('bot.reactions.footer'),
                    __('bot.reactions.description'));
                foreach (Reaction::all() as $reaction) {
                    $embed->addField(['name' =>  $reaction->trigger, 'value' => $reaction->reaction, 'inline' => true]);
                }
                $message->channel->sendEmbed($embed);
            }
        });
    }



}
