<?php

namespace App\Discord\Moderation\MediaFilter;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use App\Models\MediaChannel;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class DeleteMediaChannel extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::MEDIA;
    }

    public function trigger(): string
    {
        return 'delmediachannel';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delmediachannel');
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
        if (!MediaChannel::where('channel', $this->arguments[0])->first()) {
            return EmbedFactory::failedEmbed(__('bot.media.not-exists', ['channel' => "<#{$this->arguments[0]}>"]));
        }

        MediaChannel::where(['channel' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id])->first()->delete();
        Bot::get()->getGuild($this->guildId)->delMediaChannel($this->arguments[0]);
        return EmbedFactory::successEmbed(__('bot.media.deleted', ['channel' => "<#{$this->arguments[0]}>"]));

    }
}
