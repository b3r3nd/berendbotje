<?php

namespace App\Discord\Test\Commands;

use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Test\Jobs\ProcessTest;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class Test extends SlashCommand
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
        $this->description = 'test';
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => false,
            ],
        ];

        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function action(): MessageBuilder
    {

        $options = $this->interaction->data->options;

        ProcessTest::dispatch()->delay(now()->addSeconds(2));
        return MessageBuilder::new()->setContent("EZ!");
    }
}
