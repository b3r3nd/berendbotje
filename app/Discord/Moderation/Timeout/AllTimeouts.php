<?php

namespace App\Discord\Moderation\Timeout;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Models\Timeout;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class AllTimeouts extends SlashIndexCommand
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
        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->perPage = 5;
        $this->total = Timeout::byGuild($this->guildId)->count();
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.timeout.title'))
            ->setFooter(__('bot.timeout.footer'));

        $embed = $embedBuilder->getEmbed();
        $description = __('bot.timeout.count', ['count' => $this->total]) . "\n\n";
        foreach (Timeout::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->orderBy('created_at', 'desc')->get() as $timeout) {
            $description .= TimeoutHelper::timeoutLength($timeout);
        }
        $embed->setDescription($description);

        return $embed;
    }
}
