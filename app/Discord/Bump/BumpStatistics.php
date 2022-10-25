<?php

namespace App\Discord\Bump;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Models\Bumper;
use Discord\Builders\MessageBuilder;

class BumpStatistics extends SlashCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'bumpstats';
    }

    public function action(): MessageBuilder
    {
        $description = "";
        foreach (Bumper::orderBy('count', 'desc')->limit(20)->get() as $bumper) {
            $description .= "**{$bumper->user->discord_tag}** •  {$bumper->count}\n";
        }
        $embedBuilder = EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.bump.title'))
            ->setFooter(__('bot.bump.footer'))
            ->setDescription(__('bot.bump.description', ['bumpers' => $description]));

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
