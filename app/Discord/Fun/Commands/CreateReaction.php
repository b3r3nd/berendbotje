<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Fun\Models\Reaction;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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
                'description' => __('bot.trigger'),
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'reaction',
                'description' => __('bot.reaction'),
                'type' => Option::STRING,
                'required' => true,
            ]
        ];
        parent::__construct();
    }


    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $reaction = Reaction::create(['trigger' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction'), 'guild_id' => \App\Discord\Core\Models\Guild::get($this->guildId)->id]);
        $reaction->save();
        return EmbedFactory::successEmbed($this, __('bot.reactions.saved', ['name' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction')]));
    }
}
