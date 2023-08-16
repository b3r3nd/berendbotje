<?php

namespace App\Discord\Message\Commands\Level;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Message\Models\CustomMessage;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class DeleteLevelMessage extends SlashCommand
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
        $this->description = __('bot.slash.delete-level-msg');
        $this->slashCommandOptions = [
            [
                'name' => 'level',
                'description' => __('bot.level'),
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
            'level' => $this->getOption('level'),
            'guild_id' => Guild::get($this->guildId)->id,
        ])->first();

        if (!$customMessage) {
            return EmbedFactory::failedEmbed($this, __('bot.msg.level.not-found', ['level' => $this->getOption('level')]));
        }

        $customMessage->delete();
        return EmbedFactory::successEmbed($this, __('bot.msg.level.deleted', ['level' => $this->getOption('level')]));
    }

    public function autoComplete(Interaction $interaction): array
    {
        return CustomMessage::level($interaction->guild_id)->where('level', 'LIKE', "%{$this->getOption('level')}%")
            ->limit(25)
            ->get()
            ->map(function ($modelInstance) {
                return ['name' => $modelInstance->level, 'value' => $modelInstance->level];
            })->toArray();
    }
}
