<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Illuminate\Database\Eloquent\Builder;

class ModeratorStatistics extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'modstats';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.modstats');
        parent::__construct();
    }

    private function getCounter(string $counter, Guild $guild)
    {
        return DiscordUser::whereHas($counter, function (Builder $query) use ($guild) {
            $query->where('guild_id', '=', $guild->id);
        })->get();
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create($this->discord)
            ->setTitle(__('bot.adminstats.title'))
            ->setFooter(__('bot.adminstats.footer'));
        $description = __('bot.adminstats.description');

        $guild = Guild::get($this->guildId);
        $kicks = $this->getCounter('kickCounters', $guild);
        $bans = $this->getCounter('banCounters', $guild);
        $timeouts = $this->getCounter('givenTimeouts', $guild);

        foreach ($kicks->merge($bans)->merge($timeouts) as $moderator) {
            $bans = $moderator->banCounters->where('guild_id', $guild->id)->first()->count ?? 0;
            $kicks = $moderator->kickCounters->where('guild_id', $guild->id)->first()->count ?? 0;
            $description .= "**Moderator**: {$moderator->tag()}
            **Kicks**: {$kicks}
            **Bans**: {$bans}
            **Timeouts**: {$moderator->givenTimeouts->count()}\n\n";
        }

        $embedBuilder->setDescription($description);
        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}