<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Fun\Helpers\Helper;
use App\Domain\Fun\Models\Emote;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;

class EmoteIndex extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'emotes';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.emotes');
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = Emote::byGuild($this->guildId)->count();

        $date = Emote::byGuild($this->guildId)->get()->last()?->created_at->format('Y-m-d');
        $description = __('bot.counting-since', ['date' => $date]) . "\n\n";
        foreach (Emote::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $emote) {
            $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
            $description .= "**{$emote->emote}** • {$emote->count} \n";
        }
        return EmbedBuilder::create($this, __('bot.emotes.title'), __('bot.emotes.description', ['emotes' => $description]))->getEmbed();
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
