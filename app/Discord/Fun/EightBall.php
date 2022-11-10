<?php

namespace App\Discord\Fun;

use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;

class EightBall extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return "8ball";
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;
        $this->usageString = __('bot.8ball.no-question');
    }

    public function action(): void
    {
        $options = [
            'It is certain.',
            'It is decidedly so.',
            'Without a doubt.',
            'Yes definitely.',
            'You may rely on it.',
            'As I see it, yes.',
            'As I see it, yes.',
            'Outlook good.',
            'Yes.',
            'Signs point to yes.',

            'Reply hazy, try again.',
            'Ask again later.',
            'Better not tell you now.',
            'Cannot predict now.',
            'Concentrate and ask again.',

            "Don't count on it.",
            'My reply is no.',
            'My sources say no.',
            'Outlook not so good.',
            'Very doubtful.',
        ];

        $random = rand(0, (count($options) - 1));

        $this->message->reply($options[$random]);
    }
}
