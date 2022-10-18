<?php

namespace App\Discord\Bump;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Models\Bumper;

class BumpStatistics extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'bumpstats';
    }

    public function action(): void
    {
        $description = "";
        foreach (Bumper::orderBy('count', 'desc')->limit(10)->get() as $bumper) {
            $description .= "**{$bumper->discord_username}** - {$bumper->count}\n";
        }
        $embed = EmbedBuilder::create(Bot::get()->discord(),
            __('bot.bump.title'),
            __('bot.bump.footer'),
            __('bot.bump.description', ['bumpers' => $description]));
        $this->message->channel->sendEmbed($embed);
    }
}
