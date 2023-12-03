<?php

namespace App\Discord\Fun\Commands\Count;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Domain\Fun\Models\UserCounter;
use App\Domain\Permission\Enums\Permission;
use App\Models\DiscordUser;
use App\Models\User;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

class CountUserStats extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'user';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.countuserstats');
        parent::__construct();
    }


    public function action(): MessageBuilder
    {

        $embedBuilder = EmbedBuilder::create($this, __('bot.userstats.title'));
        $embedBuilder->setDescription(__('bot.userstats.description'));

        $guild = $this->bot->getGuild($this->guildId);
        $user = \App\Domain\Discord\User::get($this->interaction->user->id);
        $counter = $user->counters->where('guild_id', $guild->model->id)->first();

        $embedBuilder->getEmbed()->addField(['name' => 'Highest Count', 'value' => $counter->highest_count ?? 0, 'inline' => true]);
        $embedBuilder->getEmbed()->addField(['name' => 'Total Count', 'value' => $counter->count ?? 0, 'inline' => true]);
        $embedBuilder->getEmbed()->addField(['name' => 'Total Fails', 'value' => $counter->fail_count ?? 0, 'inline' => true]);

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
