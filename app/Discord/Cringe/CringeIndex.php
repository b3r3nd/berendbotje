<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Models\CringeCounter;
use Discord\Builders\MessageBuilder;

class CringeIndex extends SlashCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'cringecounter';
    }

    public function action(): MessageBuilder
    {
        $description = "";
        foreach (CringeCounter::orderBy('count', 'desc')->limit(20)->get() as $cringeCounter) {
            $description .= "**{$cringeCounter->user->discord_tag}** • {$cringeCounter->count} \n";
        }
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.cringe.title'))
            ->setFooter(__('bot.cringe.footer'))
            ->setDescription(__('bot.cringe.description', ['users' => $description]));

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
