<?php

namespace App\Discord\Fun\Reaction;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\Guild;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class DeleteReaction extends SlashAndMessageCommand
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
        parent::__construct();
        $this->requiredArguments = 1;
        $this->usageString = __('bot.reactions.usage-delreaction');
        $this->slashCommandOptions = [
            [
                'name' => 'trigger',
                'description' => 'Trigger',
                'type' => Option::STRING,
                'required' => true,
            ]
        ];
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {
        Reaction::where(['trigger' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id])->delete();
        Bot::get()->getGuild($this->guildId)->deleteReaction($this->arguments[0]);
        return EmbedFactory::successEmbed(__('bot.reactions.deleted', ['name' => $this->arguments[0]]));
    }
}
