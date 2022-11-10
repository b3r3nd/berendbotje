<?php

namespace App\Discord\Moderation\MediaFilter;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashAndMessageCommand;
use App\Models\Guild;
use App\Models\MediaChannel;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class CreateMediaChannel extends SlashAndMessageCommand
{

    public function permission(): Permission
    {
        return Permission::MEDIA;
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

        MediaChannel::create(['channel' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id]);
        Bot::get()->getGuild($this->guildId)->addMediaChannel($this->arguments[0]);
        return EmbedFactory::successEmbed(__('bot.media.added', ['channel' => $this->arguments[0]]));

    }
}
