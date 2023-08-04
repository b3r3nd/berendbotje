<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class ResetXp extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_XP;
    }

    public function trigger(): string
    {
        return 'reset';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.reset-xp');
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

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $user = User::get($this->getOption('user_mention'));
        $guild = Guild::get($this->guildId);
        $messageCounters = $user->messageCounters()->where('guild_id', $guild->id)->get();

        if ($messageCounters->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.xp.not-exist', ['user' => $this->getOption('user_mention')]));
        }

        $messageCounters->first()->delete();
        return EmbedFactory::successEmbed($this, __('bot.xp.reset', ['user' => $this->getOption('user_mention')]));
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
