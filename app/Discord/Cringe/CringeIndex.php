<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Models\CringeCounter;

class CringeIndex extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'cringecounter';
    }

    public function action(): void
    {
        $description = "";
        foreach (CringeCounter::orderBy('count', 'desc')->limit(10)->get() as $cringeCounter) {
            $description .= "**{$cringeCounter->discord_username}** - {$cringeCounter->count} \n";
        }
        $embed = EmbedBuilder::create(Bot::getDiscord(),
            __('bot.cringe.title'),
            __('bot.cringe.footer'),
            __('bot.cringe.description', ['users' => $description])
        );
        $this->message->channel->sendEmbed($embed);
    }
}
