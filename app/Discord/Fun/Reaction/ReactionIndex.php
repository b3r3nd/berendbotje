<?php

namespace App\Discord\Fun\Reaction;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Models\Reaction;
use Discord\Parts\Embed\Embed;

class ReactionIndex extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'reactions';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.reactions');
        parent::__construct();
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
