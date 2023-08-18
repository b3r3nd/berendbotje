<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\SlashCommand;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

class Vote extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'vote';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.vote');
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        return MessageBuilder::new()->setContent("https://top.gg/bot/651378995245613056/vote");
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
