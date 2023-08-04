<?php

namespace App\Discord\Moderation\Commands\Message;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Fun\Models\CustomMessage;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class AddWelcomeMessage extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::MESSAGES;
    }

    public function trigger(): string
    {
        return 'add';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.add-welcome-msg');
        $this->slashCommandOptions = [
            [
                'name' => 'message',
                'description' => __('bot.message'),
                'type' => Option::STRING,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        CustomMessage::create([
            'message' => $this->getOption('message'),
            'guild_id' => Guild::get($this->guildId)->id
        ]);
        return EmbedFactory::successEmbed($this, __('bot.msg.welcome.saved', ['message' => $this->getOption('message')]));
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
