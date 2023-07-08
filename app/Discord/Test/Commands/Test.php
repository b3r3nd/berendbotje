<?php

namespace App\Discord\Test\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Test\Jobs\ProcessTest;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class Test extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return "testing";
    }

    public function __construct()
    {
        $this->description = "test";
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function action(): MessageBuilder
    {
        return EmbedFactory::successEmbed($this, "test");
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
