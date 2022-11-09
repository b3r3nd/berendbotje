<?php

namespace App\Discord\Moderation\Timeout;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Timeout;
use Discord\Parts\Embed\Embed;

class AllTimeouts extends SlashAndMessageIndexCommand
{
    public function permission(): string
    {
        return 'timeouts';
    }

    public function trigger(): string
    {
        return 'timeouts';
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
        foreach (Timeout::byGuild($this->guildId)->skip($this->offset)->limit($this->perPage)->orderBy('created_at', 'desc')->get() as $timeout) {
            $description .= TimeoutHelper::timeoutLength($timeout);
        }
        $embed->setDescription($description);

        return $embed;
    }
}
