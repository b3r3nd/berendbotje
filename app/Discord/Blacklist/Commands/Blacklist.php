<?php

namespace App\Discord\Blacklist\Commands;

use App\Discord\Blacklist\Models\Abuser;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class Blacklist extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::BLACKLIST;
    }

    public function trigger(): string
    {
        return 'blacklist';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.blacklist');
        parent::__construct();
    }


    public function getEmbed(): Embed
    {
        $this->total = Abuser::byDiscordGuildId($this->guildId)->count();
        $description = "";
        foreach (Abuser::byDiscordGuildId(($this->guildId))->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $abuser) {
            $description .= "{$abuser->created_at} - <@{$abuser->discord_id}> - {$abuser->reason}\n";
        }
        return EmbedBuilder::create($this, __('bot.blacklist.title'), $description)->getEmbed();
    }
}
