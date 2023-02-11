<?php

namespace App\Discord\OpenAi;

use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Jobs\ProcessImageGenration;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class GenerateImage extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::OPENAI;
    }

    public function trigger(): string
    {
        return 'image';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.gen-image');
        $this->slashCommandOptions = [
            [
                'name' => 'prompt',
                'description' => 'prompt',
                'type' => Option::STRING,
                'required' => true,
            ],
        ];

        parent::__construct();

    }

    public function action(): MessageBuilder
    {
        ProcessImageGenration::dispatch($this->interaction->channel_id, $this->arguments[0]);
        return MessageBuilder::new()->setContent("Generating Image with prompt _{$this->arguments[0]}_");
    }
}
