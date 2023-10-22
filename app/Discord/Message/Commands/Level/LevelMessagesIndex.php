<?php

namespace App\Discord\Message\Commands\Level;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Message\Enums\CustomMessage as CustomMessageEnum;
use App\Domain\Message\Models\CustomMessage;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;

class LevelMessagesIndex extends SlashIndexCommand
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
        $this->description = __('bot.slash.level-index');
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->total = CustomMessage::byGuild($this->guildId)->count();
        $description = "";
        foreach (CustomMessage::byGuild($this->guildId)->where('type', CustomMessageEnum::LEVEL->value)->orderBy('level')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $message) {
            $description .= "**{$message->level}** • {$message->message} \n";
        }
        return EmbedBuilder::create($this)
            ->setTitle(__('bot.msg.level.title'))
            ->setDescription($description)
            ->getEmbed();
    }

    public function autoComplete(Interaction $interaction): array
    {
       return [];
    }
}
