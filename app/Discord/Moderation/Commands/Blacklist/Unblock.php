<?php

namespace App\Discord\Moderation\Commands\Blacklist;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Moderation\Models\Abuser;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class Unblock extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::BLACKLIST;
    }

    public function trigger(): string
    {
        return 'unblock';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.unblock');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => __('bot.user-mention'),
                'type' => Option::USER,
                'required' => true,
            ],
        ];

        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $userId = $this->getOption('user_mention');

        if (Abuser::where('discord_id', $userId)->get()->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.blacklist.unblocked', ['user' => "<@{$userId}>"]));
        }

        $abusers = Abuser::where('discord_id', $userId)->first();
        $abusers->delete();
        return EmbedFactory::successEmbed($this, __('bot.blacklist.unblock', ['user' => "<@{$userId}>"]));
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
