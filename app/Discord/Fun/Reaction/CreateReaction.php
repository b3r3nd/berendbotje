<?php

namespace App\Discord\Fun\Reaction;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
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
        $reaction = Reaction::create(['trigger' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction'), 'guild_id' => \App\Models\Guild::get($this->guildId)->id]);
        $reaction->save();
        return EmbedFactory::successEmbed($this->discord, __('bot.reactions.saved', ['name' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction')]));
    }
}
