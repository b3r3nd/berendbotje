<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Fun\Models\CringeCounter;
use App\Discord\Levels\Helpers\Helper;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Exception;

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

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = CringeCounter::byGuild($this->guildId)->count();

        $description = "";
        foreach (CringeCounter::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $cringeCounter) {
            $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
            $description .= "**{$cringeCounter->user->tag()}** • {$cringeCounter->count} \n";
        }
        return EmbedBuilder::create($this)
            ->setTitle(__('bot.cringe.title'))
            ->setDescription(__('bot.cringe.description', ['users' => $description]))
            ->getEmbed();
    }
}
