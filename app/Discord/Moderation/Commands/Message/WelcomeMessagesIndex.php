<?php

namespace App\Discord\Moderation\Commands\Message;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Fun\Enums\CustomMessage as CustomMessageEnum;
use App\Domain\Fun\Models\CustomMessage;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;

class WelcomeMessagesIndex extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::MESSAGES;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.welcome-index');
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->total = CustomMessage::byGuild($this->guildId)->count();
        $description = "";
        foreach (CustomMessage::byGuild($this->guildId)->where('type', CustomMessageEnum::WELCOME->value)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $message) {
            $description .= "**{$message->id}** • {$message->message} \n";
        }
        return EmbedBuilder::create($this)
            ->setTitle(__('bot.msg.welcome.title'))
            ->setDescription($description)
            ->getEmbed();
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
