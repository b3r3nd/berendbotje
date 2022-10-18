<?php

namespace App\Discord\SimpleReaction;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Models\Reaction;

class ReactionsIndex extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'reactions';
    }

    public function action(): void
    {
        $embed = EmbedBuilder::create(Bot::getDiscord(),
            __('bot.reactions.title'),
            __('bot.reactions.footer'),
            __('bot.reactions.description'));
        foreach (Reaction::all() as $reaction) {
            $embed->addField(['name' => $reaction->trigger, 'value' => $reaction->reaction, 'inline' => true]);
        }
        $this->message->channel->sendEmbed($embed);
    }
}
