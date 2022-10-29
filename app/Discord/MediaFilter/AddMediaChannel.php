<?php

namespace App\Discord\MediaFilter;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\MediaChannel;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class AddMediaChannel extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'addmediachannel';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->usageString = __('bot.media.usage-addmedia');
        $this->slashCommandOptions = [
            [
                'name' => 'channel',
                'description' => 'Channel',
                'type' => Option::CHANNEL,
                'required' => true,
            ],
        ];

        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        if (!preg_match('/(<#)/', $this->arguments[0])) {
            return EmbedFactory::failedEmbed(__('bot.media.no-channel', ['channel' => $this->arguments[0]]));
        }
        if (MediaChannel::where('channel', $this->arguments[0])->first()) {
            return EmbedFactory::failedEmbed(__('bot.media.exists', ['channel' => $this->arguments[0]]));
        }

        MediaChannel::create(['channel' => $this->arguments[0]]);
        Bot::get()->addMediaChannel($this->arguments[0]);
        return EmbedFactory::successEmbed(__('bot.media.added', ['channel' => $this->arguments[0]]));

    }
}
