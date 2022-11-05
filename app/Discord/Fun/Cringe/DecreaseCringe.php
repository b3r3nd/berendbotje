<?php

namespace App\Discord\Fun\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class DecreaseCringe extends SlashAndMessageCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'delcringe';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->requiresMention = true;
        $this->usageString = __('bot.cringe.usage-delcringe');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => true,
            ],
        ];

        parent::__construct();
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {
        $user = DiscordUser::getByGuild($this->arguments[0], $this->guildId);

        if (isset($user->cringeCounter)) {
            $user->cringeCounter->count = $user->cringeCounter->count - 1;
            if ($user->cringeCounter->count == 0) {
                $user->cringeCounter->delete();
            } else {
                $user->cringeCounter->save();
            }
        } else {
            return EmbedFactory::failedEmbed(__('bot.cringe.not-cringe', ['name' => "<@{$this->arguments[0]}>"]));
        }
        return EmbedFactory::successEmbed(__('bot.cringe.change', ['name' => $user->tag(), 'count' => $user->cringeCounter->count]));
    }
}
