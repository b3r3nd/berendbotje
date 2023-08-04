<?php

namespace App\Discord\Fun\Commands\Reaction;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Fun\Models\Reaction;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;

class ReactionIndex extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.reactions');
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = Reaction::byGuild($this->guildId)->count();
        $this->perPage = 20;
        $embedBuilder = EmbedBuilder::create($this, __('bot.reactions.title'), __('bot.reactions.description'));
        foreach (Reaction::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $reaction) {
            $embedBuilder->getEmbed()->addField(['name' => $reaction->trigger, 'value' => $reaction->reaction, 'inline' => true]);
        }
        return $embedBuilder->getEmbed();
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
