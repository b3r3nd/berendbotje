<?php

namespace App\Discord\Levels\Commands\DurationReward;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Levels\Jobs\ProcessRoles;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Carbon;

class SyncDurationRewards extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::CONFIG;
    }

    public function trigger(): string
    {
        return 'sync';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.give-role');
        parent::__construct();
    }


    public function action(): MessageBuilder
    {
        ProcessRoles::dispatch($this->guildId);
        return EmbedFactory::successEmbed($this, __('bot.process'));
    }



    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
