<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\MentionResponder\Models\MentionReply;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $mentionReply = MentionReply::find($this->getOption('reply'));
        if (!$mentionReply) {
             return EmbedFactory::failedEmbed($this, __('bot.mention.no-reply'));
        }
        $mentionReply->delete();
        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed($this, __('bot.mention.deleted'));
    }
}
