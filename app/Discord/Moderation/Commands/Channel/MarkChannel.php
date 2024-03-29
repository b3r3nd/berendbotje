<?php

namespace App\Discord\Moderation\Commands\Channel;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Moderation\Actions\FlagChannelAction;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class MarkChannel extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::CHANNEL;
    }

    public function trigger(): string
    {
        return 'flag';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.mark-channel');
        $this->slashCommandOptions = [
            [
                'name' => 'channel',
                'description' => __('bot.channel'),
                'type' => Option::CHANNEL,
                'required' => true,
            ],
            [
                'name' => 'flag',
                'description' => __('bot.flags'),
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

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        (new FlagChannelAction($this->interaction->data->options, $this->guildId, true, $this->bot))->execute();
        return EmbedFactory::successEmbed($this, __('bot.channels.added', ['channel' => $this->getOption('channel'), 'flag' => $this->getOption('flag')]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
