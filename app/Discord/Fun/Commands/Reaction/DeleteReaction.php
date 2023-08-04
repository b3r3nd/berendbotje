<?php

namespace App\Discord\Fun\Commands\Reaction;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Fun\Models\Reaction;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class DeleteReaction extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::REACTIONS;
    }

    public function trigger(): string
    {
        return 'delete';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.del-reaction');
        $this->slashCommandOptions = [
            [
                'name' => 'trigger',
                'description' => __('bot.trigger'),
                'type' => Option::STRING,
                'required' => true,
                'autocomplete' => true,
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
        Reaction::where(['trigger' => $this->getOption('trigger'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this, __('bot.reactions.deleted', ['name' => $this->getOption('trigger')]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return $this->getAutoComplete(Reaction::class, $interaction->guild_id, 'trigger', $this->getOption('trigger'));
    }
}
