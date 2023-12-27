<?php

namespace App\Discord\Fun\Commands\Count;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Domain\Fun\Models\UserCounter;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

class CountStats extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'stats';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.countstats');

        // hoi richard

        parent::__construct();
    }


    public function action(): MessageBuilder
    {

        $embedBuilder = EmbedBuilder::create($this, __('bot.countstats.title'));
        $embedBuilder->setDescription(__('bot.countstats.description'));


        $guild = $this->bot->getGuild($this->guildId);
        $counters = UserCounter::where('guild_id', $guild->model->id);
        $total = $counters->sum('count');
        $highest = $counters->max('highest_count');
        $fails = $counters->sum('fail_count');

        $embedBuilder->getEmbed()->addField(['name' => 'Highest Count', 'value' => $highest, 'inline' => true]);
        $embedBuilder->getEmbed()->addField(['name' => 'Total Count', 'value' => $total, 'inline' => true]);
        $embedBuilder->getEmbed()->addField(['name' => 'Total Fails', 'value' => $fails, 'inline' => true]);

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
