<?php

namespace App\Discord\Timeout;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Timeout;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class SingleUserTimeouts extends SlashAndMessageIndexCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'usertimeouts';
    }

    public function __construct()
    {
        $this->requiresMention = true;
        $this->requiredArguments = 1;
        $this->usageString = __('bot.timeout.usage-timeouts');

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


    public function getEmbed(): Embed
    {
        $this->total = Timeout::where(['discord_id' => $this->arguments[0]])->count();
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.timeout.title'))
            ->setFooter(__('bot.timeout.footer'));

        $embed = $embedBuilder->getEmbed();
        $embed->setDescription(__('bot.timeout.count', ['count' => $this->total]));
        foreach (Timeout::where(['discord_id' => $this->arguments[0]])->skip($this->offset)->limit($this->perPage)->orderBy('created_at', 'desc')->get() as $timeout) {
            $embed = TimeoutHelper::timeoutLength($embed, $timeout);
        }

        return $embed;
    }
}
