<?php

namespace App\Discord;

use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Jobs\ProcessTest;
use Discord\Builders\MessageBuilder;

class JustinTest extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return "justin";
    }

    public function __construct()
    {
        $this->description = "Justin!!!!!!";
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function action(): MessageBuilder
    {
        ProcessTest::dispatch()->delay(now()->addSeconds(5));
        return MessageBuilder::new()->setContent("Loading...");
    }
}
