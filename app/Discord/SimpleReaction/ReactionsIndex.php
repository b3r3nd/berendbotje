<?php

namespace App\Discord\SimpleReaction;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;

class ReactionsIndex extends SlashCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'reactions';
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.reactions.title'))
            ->setFooter(__('bot.reactions.footer'))
            ->setDescription(__('bot.reactions.description'));
        foreach (Reaction::all() as $reaction) {
            $embedBuilder->getEmbed()->addField(['name' => $reaction->trigger, 'value' => $reaction->reaction, 'inline' => true]);
        }

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
