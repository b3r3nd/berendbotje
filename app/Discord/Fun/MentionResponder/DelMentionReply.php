<?php

namespace App\Discord\Fun\MentionResponder;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\MentionGroup;
use App\Models\MentionReply;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class DelMentionReply extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ADD_MENTION;
    }

    public function trigger(): string
    {
        return 'delreply';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delreply');

        $this->slashCommandOptions = [
            [
                'name' => 'reply',
                'description' => 'Reply ID',
                'type' => Option::INTEGER,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $mentionReply = MentionReply::find($this->arguments[0]);
        if (!$mentionReply) {
            return EmbedFactory::failedEmbed(__('bot.mention.no-reply'));
        }
        $mentionReply->delete();
        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed(__('bot.mention.deleted'));
    }
}
