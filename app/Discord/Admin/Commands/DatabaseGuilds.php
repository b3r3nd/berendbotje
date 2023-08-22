<?php

namespace App\Discord\Admin\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Discord\Guild;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;

class DatabaseGuilds extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::CONFIG;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.guilds');
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->total = Guild::all()->count();
        $description = "";
        foreach (Guild::skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $guild) {
            $description .= "{$guild->name} - {$guild->owner->tag()} \n";
        }
        return EmbedBuilder::create($this, __('bot.guilds.title', ['count' => $this->total]), $description)->getEmbed();
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
