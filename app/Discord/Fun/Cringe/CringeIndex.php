<?php

namespace App\Discord\Fun\Cringe;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Helper;
use App\Models\CringeCounter;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class CringeIndex extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'cringecounter';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.cringecounter');
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->total = CringeCounter::byGuild($this->guildId)->count();

        $description = "";
        foreach (CringeCounter::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $cringeCounter) {
            $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
            $description .= "**{$cringeCounter->user->tag()}** • {$cringeCounter->count} \n";
        }
        return EmbedBuilder::create($this->discord)
            ->setTitle(__('bot.cringe.title'))
            ->setFooter(__('bot.cringe.footer'))
            ->setDescription(__('bot.cringe.description', ['users' => $description]))
            ->getEmbed();
    }
}
