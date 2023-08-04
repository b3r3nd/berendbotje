<?php

namespace App\Discord\Fun\Commands\Cringe;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Fun\Helpers\Helper;
use App\Domain\Fun\Models\CringeCounter;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;

class CringeIndex extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'counter';
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

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
