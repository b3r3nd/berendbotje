<?php

namespace App\Discord\Reaction\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Reaction\Models\Reaction;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class CreateReaction extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::REACTIONS;
    }

    public function trigger(): string
    {
        return 'addreaction';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.add-reaction');
        $this->slashCommandOptions = [
            [
                'name' => 'trigger',
                'description' => 'Trigger',
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'reaction',
                'description' => 'Reaction',
                'type' => Option::STRING,
                'required' => true,
            ]
        ];
        parent::__construct();
    }


    /**
     * @return MessageBuilder
     */
    public function action(): MessageBuilder
    {
        $reaction = Reaction::create(['trigger' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction'), 'guild_id' => \App\Discord\Core\Models\Guild::get($this->guildId)->id]);
        $reaction->save();
        return EmbedFactory::successEmbed($this->discord, __('bot.reactions.saved', ['name' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction')]));
    }
}
