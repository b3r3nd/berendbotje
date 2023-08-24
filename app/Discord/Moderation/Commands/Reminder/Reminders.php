<?php

namespace App\Discord\Moderation\Commands\Reminder;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Moderation\Models\Reminder;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;

class Reminders extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::REMINDERS;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.reminders');
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->perPage = 5;
        $this->total = Reminder::byGuild($this->guildId)->count();

        $description = "";
        foreach (Reminder::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $reminder) {
            $description .= "**ID**: {$reminder->id}\n **Interval**: {$reminder->interval} \n **Channel**: <#{$reminder->channel}>\n **Message**: {$reminder->message}\n\n";
        }
        return EmbedBuilder::create($this, __('bot.reminders.title'), $description)->getEmbed();
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
