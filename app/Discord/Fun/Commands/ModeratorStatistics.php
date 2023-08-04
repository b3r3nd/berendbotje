<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Exception;
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
        return User::whereHas($counter, function (Builder $query) use ($guild) {
            $query->where('guild_id', '=', $guild->id);
        })->get();
    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create($this, __('bot.adminstats.title'));
        $embedBuilder->setDescription(__('bot.adminstats.description'));

        $guild = Guild::get($this->guildId);
        $kicks = $this->getCounter('kickCounters', $guild);
        $bans = $this->getCounter('banCounters', $guild);
        $timeouts = $this->getCounter('givenTimeouts', $guild);

        foreach ($kicks->merge($bans)->merge($timeouts) as $moderator) {
            $bans = $moderator->banCounters->where('guild_id', $guild->id)->first()->count ?? 0;
            $kicks = $moderator->kickCounters->where('guild_id', $guild->id)->first()->count ?? 0;
            $description = "{$moderator->tag()} \n **Kicks**: {$kicks} \n **Bans**: {$bans} \n **Timeouts**: {$moderator->givenTimeouts->count()}";

            $embedBuilder->getEmbed()->addField(
                ['name' => "", 'value' => $description, 'inline' => true],
            );
        }

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
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
