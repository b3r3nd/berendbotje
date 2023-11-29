<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use App\Domain\Fun\Helpers\Helper;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class UserRank extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'rank';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.rank');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => __('bot.user-mention'),
                'type' => Option::USER,
                'required' => false,
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
        if ($this->getOption('user_mention')) {
            $user = User::get($this->getOption('user_mention'));
        } else {
            $user = User::get($this->interaction->member);
        }

        $guild = Guild::get($this->guildId);
        $messageCounters = $user->messageCounters()->where('guild_id', $guild->id)->get();
        if ($messageCounters->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.xp.not-found', ['user' => $user->tag()]));
        }

        $messageCounter = $messageCounters->first();
        $voice = $messageCounter->voice_seconds / 60;
        if ($voice >= 60) {
            $voice = round($voice / 60);
            $voice = "{$voice} hours";
        } else {
            $voice = round($voice);
            $voice = "{$voice} minutes";
        }

        $nextLevelXp = Helper::getXpForLevel($messageCounter->level);
        $xpForNextRank = $nextLevelXp;
        $currentXp = $messageCounter->xp - Helper::calcRequiredXp($messageCounter->level);

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create($this)
            ->setDescription(__('bot.xp.description', ['user' => $user->tag(), 'messages' => $messageCounter->count,
                'xp' => Helper::format($messageCounter->xp), 'voice' => $voice, 'level' => $messageCounter->level,
                'xpNextRank' => Helper::format($xpForNextRank), 'currentXp' => Helper::format($currentXp)]))
            ->setTitle(__('bot.xp.title', ['level' => $messageCounter->level, 'xp' => $messageCounter->xp]))
            ->getEmbed());
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
