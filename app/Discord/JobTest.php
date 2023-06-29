<?php

namespace App\Discord;

use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Jobs\ProcessTest;
use Discord\Builders\MessageBuilder;

class JobTest extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return "test";
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
        ProcessTest::dispatch()->delay(now()->addSeconds(2));
        return MessageBuilder::new()->setContent("Loading...");
    }
}
