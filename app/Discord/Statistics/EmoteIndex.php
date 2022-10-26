<?php

namespace App\Discord\Statistics;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Discord\Helper;
use App\Models\Emote;
use Discord\Builders\MessageBuilder;

class EmoteIndex extends SlashCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'emotes';
    }

    public function action(): MessageBuilder
    {
        $description = "";
        foreach (Emote::orderBy('count', 'desc')->limit(20)->get() as $index => $emote) {
            $description .= Helper::topThree($index);
            $description .= "**{$emote->emote}** • {$emote->count} \n";
        }
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.emotes.title'))
            ->setFooter(__('bot.emotes.footer'))
            ->setDescription(__('bot.emotes.description', ['emotes' => $description]));

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
