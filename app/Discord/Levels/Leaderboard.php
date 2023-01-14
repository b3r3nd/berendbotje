<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Helper;
use Discord\Parts\Embed\Embed;

class Leaderboard extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'leaderboard';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.leaderboard');
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->total = \App\Models\MessageCounter::byGuild($this->guildId)->count();

        $description = "";
        foreach (\App\Models\MessageCounter::byGuild($this->guildId)->orderBy('xp', 'desc')->skip($this->getOffset())->limit($this->perPage)->get() as $index => $messageCounter) {
            $description .= Helper::indexPrefix($index, $this->getOffset());
            $description .= "Level **{$messageCounter->level}** • {$messageCounter->user->tag()} • {$messageCounter->xp} xp \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.messages.title'))
            ->setFooter(__('bot.messages.footer', ['xp' => Bot::get()->getGuild($this->guildId)->getSetting(Setting::XP_COUNT)]))
            ->setDescription(__('bot.messages.description', ['users' => $description]))
            ->getEmbed();
    }
}
