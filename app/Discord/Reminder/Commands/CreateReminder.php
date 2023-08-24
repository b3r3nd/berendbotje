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

class CreateReminder extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::REMINDERS;
    }

    public function trigger(): string
    {
        return 'create';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.create-reminder');
        $this->slashCommandOptions = [
            [
                'name' => 'interval',
                'description' => __('bot.interval'),
                'type' => Option::INTEGER,
                'required' => true,
            ],
            [
                'name' => 'channel',
                'description' => __('bot.channel'),
                'type' => Option::CHANNEL,
                'required' => true,
            ],
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
        Reminder::create([
            'interval' => $this->getOption('interval'),
            'channel' => $this->getOption('channel'),
            'message' => $this->getOption('message'),
            'guild_id' => Guild::get($this->guildId)->id,
        ]);

        return EmbedFactory::successEmbed($this, __('bot.reminders.created', [
            'interval' => $this->getOption('interval'),
            'channel' => $this->getOption('channel'),
            'message' => $this->getOption('message'),
        ]));
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
