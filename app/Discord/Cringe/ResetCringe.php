<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class ResetCringe extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'resetcringe';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->usageString = __('bot.cringe.usage-resetcringe');
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

    public function action(): MessageBuilder
    {
        /**
         * @TODO Try to add this to the abstract class when people leave the server this is the only way to get ID?
         */
        if (str_contains($this->arguments[0], '<@')) {
            $this->arguments[0] = str_replace(['<', '>', '@'], '', $this->arguments[0]);
        }
        $user = DiscordUser::where(['discord_id' => $this->arguments[0]])->first();

        if (!isset($user->cringeCounter)) {
            return EmbedFactory::failedEmbed(__('bot.cringe.not-cringe', ['name' => "<@{$this->arguments[0]}>"]));
        }

        $user->cringeCounter->delete();
        return EmbedFactory::successEmbed(__('bot.cringe.reset', ['user' => $user->tag()]));
    }
}
