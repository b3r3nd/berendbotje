<?php

namespace App\Discord\Fun\Reaction;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class DeleteReaction extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::REACTIONS;
    }

    public function trigger(): string
    {
        return 'delreaction';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.del-reaction');
        $this->slashCommandOptions = [
            [
                'name' => 'trigger',
                'description' => 'Trigger',
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
        Reaction::where(['trigger' => $this->getOption('trigger'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this->discord, __('bot.reactions.deleted', ['name' => $this->getOption('trigger')]));
    }
}
