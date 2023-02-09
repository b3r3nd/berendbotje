<?php

namespace App\Discord\Moderation\Timeout;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Models\Timeout;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class Timeouts extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::TIMEOUTS;
    }

    public function trigger(): string
    {
        return 'timeouts';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.timeouts');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => false,
            ],
        ];
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->perPage = 5;
        if(isset($this->arguments[0])) {
            $timeouts = Timeout::byGuild($this->guildId)->where(['discord_id' => $this->arguments[0]])->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->orderBy('created_at', 'desc')->get();
        } else {
            $timeouts = Timeout::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->orderBy('created_at', 'desc')->get();
        }

        $this->total = $timeouts->count();
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.timeout.title'))
            ->setFooter(__('bot.timeout.footer'));

        $embed = $embedBuilder->getEmbed();
        $description = __('bot.timeout.count', ['count' => $this->total]) . "\n\n";
        foreach ($timeouts as $timeout) {
            $description .= TimeoutHelper::timeoutLength($timeout);
        }
        $embed->setDescription($description);

        return $embed;
    }
}