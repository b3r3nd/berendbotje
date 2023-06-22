<?php

namespace App\Discord\Moderation\Channels;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Channel;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class MarkChannel extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::CHANNEL;
    }

    public function trigger(): string
    {
        return 'markchannel';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.mark-channel');
        $this->slashCommandOptions = [
            [
                'name' => 'channel',
                'description' => 'Channel',
                'type' => Option::CHANNEL,
                'required' => true,
            ],
            [
                'name' => 'flag',
                'description' => 'flags',
                'type' => Option::STRING,
                'required' => true,
                'choices' => [
                    ['name' => 'Gain no XP', 'value' => 'no_xp'],
                    ['name' => 'Media and URLs only', 'value' => 'media_only'],
                    ['name' => 'Delete stickers', 'value' => 'no_stickers'],
                    ['name' => 'Disable message logging', 'value' => 'no_log'],
                ]
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        (new FlagChannelAction($this->arguments, $this->guildId, true))->execute();
        return EmbedFactory::successEmbed(__('bot.channels.added', ['channel' => $this->arguments[0], 'flag' => $this->arguments[1]]));
    }
}
