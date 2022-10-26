<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Helper;
use App\Models\CringeCounter;

class CringeIndex extends SlashIndexCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'cringecounter';
    }

    public function getEmbed(): EmbedBuilder
    {
        $description = "";
        foreach (CringeCounter::orderBy('count', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $cringeCounter) {
            $description .= Helper::indexPrefix($index, $this->offset);
            $description .= "**{$cringeCounter->user->discord_tag}** • {$cringeCounter->count} \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.cringe.title'))
            ->setFooter(__('bot.cringe.footer'))
            ->setDescription(__('bot.cringe.description', ['users' => $description]));
    }
}
