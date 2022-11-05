<?php

namespace App\Discord\Fun\SimpleReaction;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Reaction;
use Discord\Parts\Embed\Embed;

class ReactionIndex extends SlashAndMessageIndexCommand
{
    public function permission(): string
    {
        return "";
    }

    public function trigger(): string
    {
        return 'reactions';
    }


    public function getEmbed(): Embed
    {
        $this->total = Reaction::byGuild($this->guildId)->count();
        $this->perPage = 20;

        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.reactions.title'))
            ->setFooter(__('bot.reactions.footer'))
            ->setDescription(__('bot.reactions.description'));
        foreach (Reaction::byGuild($this->guildId)->skip($this->offset)->limit($this->perPage)->get() as $reaction) {
            $embedBuilder->getEmbed()->addField(['name' => $reaction->trigger, 'value' => $reaction->reaction, 'inline' => true]);
        }
        return $embedBuilder->getEmbed();
    }
}
