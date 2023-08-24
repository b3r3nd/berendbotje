<?php

namespace App\Discord\Reminder\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Moderation\Models\Reminder;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class DeleteReminder extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::REMINDERS;
    }

    public function trigger(): string
    {
        return 'delete';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delete-reminder');
        $this->slashCommandOptions = [
            [
                'name' => 'reminder',
                'description' => __('bot.reminder'),
                'type' => Option::INTEGER,
                'required' => true,
                'autocomplete' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        Reminder::where(['id' => $this->getOption('reminder'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this, __('bot.reminders.deleted'));
    }

    public function autoComplete(Interaction $interaction): array
    {
        return $this->getAutoComplete(Reminder::class, $interaction->guild_id, 'id', $this->getOption('reminder'));

    }
}
