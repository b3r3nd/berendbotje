<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Fun\Models\Emote;
use App\Discord\Levels\Helpers\Helper;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;
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
        $description = "";
        foreach (Emote::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $emote) {
            $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
            $description .= "**{$emote->emote}** • {$emote->count} \n";
        }
        return EmbedBuilder::create($this, __('bot.emotes.title'), __('bot.emotes.description', ['emotes' => $description]))->getEmbed();
    }

}
