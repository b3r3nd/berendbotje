<?php

namespace App\Discord\CustomMessages\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\CustomMessages\Models\CustomMessage;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class DeleteWelcomeMessage extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::MESSAGES;
    }

    public function trigger(): string
    {
        return 'delete';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delete-welcome-msg');
        $this->slashCommandOptions = [
            [
                'name' => 'message',
                'description' => __('bot.message'),
                'type' => Option::INTEGER,
                'required' => true,
                'autocomplete' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $customMessage = CustomMessage::where([
            'id' => $this->getOption('message'),
            'guild_id' => Guild::get($this->guildId)->id,
        ])->first();

        if (!$customMessage) {
            return EmbedFactory::failedEmbed($this, __('bot.msg.welcome.not-found', ['message' => $this->getOption('message')]));
        }

        $customMessage->delete();
        return EmbedFactory::successEmbed($this, __('bot.msg.welcome.deleted'));
    }

    public function autoComplete(Interaction $interaction): array
    {
        return CustomMessage::welcome($interaction->guild_id)->where('id', 'LIKE', "%{$this->getOption('message')}%")
            ->limit(25)
            ->get()
            ->map(function ($modelInstance) {
                return ['name' => substr($modelInstance->message, 0, 32), 'value' => $modelInstance->id];
            })->toArray();
    }
}
