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
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function action(): MessageBuilder
    {


        //ProcessTest::dispatch()->delay(now()->addSeconds(2));

//        $embed = EmbedBuilder::create($this->discord, $this->interaction, "Title test", "Description test");
//        return MessageBuilder::new()->addEmbed($embed);

        return EmbedFactory::successEmbed($this, "nice");
    }
}
