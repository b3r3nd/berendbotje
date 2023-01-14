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
        $reaction = Reaction::create(['trigger' => $this->arguments[0], 'reaction' => $this->arguments[1], 'guild_id' => \App\Models\Guild::get($this->guildId)->id]);
        $reaction->save();
        return EmbedFactory::successEmbed(__('bot.reactions.saved', ['name' => $this->arguments[0], 'reaction' => $this->arguments[1]]));
    }
}
