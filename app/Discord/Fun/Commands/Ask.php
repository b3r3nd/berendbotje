<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\SlashCommand;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Facades\Http;

class Ask extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'ask';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.ask');
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

    public function action(): MessageBuilder
    {
        $response = Http::get('https://yesno.wtf/api');

        return MessageBuilder::new()->setContent($this->getOption('question') . "\n" . $response->json('image'));
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
