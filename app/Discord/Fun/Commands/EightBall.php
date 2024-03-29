<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\SlashCommand;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class EightBall extends SlashCommand
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
        $this->description = __('bot.slash.8ball');
        $this->slashCommandOptions = [
            [
                'name' => 'question',
                'description' => __('bot.question'),
                'type' => Option::STRING,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function action(): MessageBuilder
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

        $random = random_int(0, (count($options) - 1));


        return MessageBuilder::new()->setContent("**{$this->getOption('question')}**\n\n{$options[$random]}");
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
