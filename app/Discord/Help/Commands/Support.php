<?php

namespace App\Discord\Help\Commands;

use App\Discord\Core\SlashCommand;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

class Support extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'support';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.support');
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        return MessageBuilder::new()->setContent("https://discord.gg/6vbxgfaUz6");
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
