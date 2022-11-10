<?php

namespace App\Discord\Levels;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Models\DiscordUser;
use App\Models\Guild;

class ResetXp extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_XP;
    }

    public function trigger(): string
    {
        return 'resetxp';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->requiresMention = 1;
        $this->usageString = __('bot.xp.usage-resetxp');
        parent::__construct();
    }

    public function action(): void
    {
        $user = DiscordUser::get($this->arguments[0]);
        $guild = Guild::get($this->message->guild_id);
        $messageCounters = $user->messageCounters()->where('guild_id', $guild->id)->get();

        if ($messageCounters->isEmpty()) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.xp.not-exist', ['user' => $this->arguments[0]])));
            return;
        }

        $tmp = $messageCounters->first()->delete();
        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.xp.reset', ['user' => $this->arguments[0]])));
    }
}
