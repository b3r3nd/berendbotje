<?php

namespace App\Discord\Fun\Commands\Reaction;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Fun\Models\Reaction;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class CreateReaction extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::REACTIONS;
    }

    public function trigger(): string
    {
        return 'add';
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
        $reaction = Reaction::create(['trigger' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction'), 'guild_id' => \App\Domain\Discord\Guild::get($this->guildId)->id]);
        $reaction->save();
        return EmbedFactory::successEmbed($this, __('bot.reactions.saved', ['name' => $this->getOption('trigger'), 'reaction' => $this->getOption('reaction')]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
