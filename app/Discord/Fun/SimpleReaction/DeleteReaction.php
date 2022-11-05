<?php

namespace App\Discord\Fun\SimpleReaction;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class DeleteReaction extends SlashAndMessageCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
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
        Reaction::where(['trigger' => $this->arguments[0], 'guild_id' => $this->guildId])->delete();
        Bot::get()->deleteReaction($this->arguments[0]);
        return EmbedFactory::successEmbed(__('bot.reactions.deleted', ['name' => $this->arguments[0]]));
    }
}
