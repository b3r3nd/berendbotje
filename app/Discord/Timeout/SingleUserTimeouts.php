<?php

namespace App\Discord\Timeout;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;
use App\Models\Timeout;

class SingleUserTimeouts extends Command
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
        parent::__construct();
        $this->requiresMention = true;
        $this->requiredArguments = 1;
        $this->usageString = __('bot.timeout.usage-timeouts');
    }

    public function action(): void
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.timeout.title'))
            ->setFooter(__('bot.timeout.footer'));

        $embed = $embedBuilder->getEmbed();
        foreach ($this->message->mentions as $mention) {
            $embed->setDescription(__('bot.timeout.count', ['count' => Timeout::where(['discord_id' => $mention->id])->count()]));
            foreach (Timeout::where(['discord_id' => $mention->id])->orderBy('created_at', 'desc')->get() as $timeout) {
                $embed = TimeoutHelper::timeoutLength($embed, $timeout);
            }
        }
        $this->message->channel->sendEmbed($embed);
    }
}
