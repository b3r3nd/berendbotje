<?php

namespace App\Discord\Statistics;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;
use App\Models\Timeout;
use Discord\Builders\MessageBuilder;

class ModeratorStatistics extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'modstats';
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.adminstats.title'))
            ->setFooter(__('bot.adminstats.footer'));

        $description = __('bot.adminstats.description');

        foreach (Admin::all() as $admin) {
            $user = $admin->user;
            $timeouts = Timeout::where('giver_id', $user->id)->count();
            $bans = $user->banCounter->count ?? 0;
            $kicks = $user->kickCounter->count ?? 0;

            $description .= "**Moderator**: <@{$user->discord_id}>\n**Bans given**: {$bans}\n**Kicks given**: {$kicks}\n**Timeouts given**: {$timeouts}\n\n";
        }

        $embedBuilder->setDescription($description);

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());

    }
}
