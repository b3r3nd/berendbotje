<?php

namespace App\Discord\Moderation;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;

class ModeratorStatistics extends SlashAndMessageCommand
{

    public function permission(): string
    {
        return "";
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

        $kickers = DiscordUser::has('kickCounters')->get();
        $banners = DiscordUser::has('banCounters')->get();
        $timeouts = DiscordUser::has('givenTimeouts')->get();

        foreach ($kickers->merge($banners)->merge($timeouts) as $moderator) {
            $description .= "**Moderator**: {$moderator->tag()}\n**Kicks**: {$moderator->kickCounters->count()}\n**Bans**: {$moderator->banCounters->count()}\n**Timeouts**: {$moderator->givenTimeouts->count()}\n\n";
        }

        $embedBuilder->setDescription($description);
        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
