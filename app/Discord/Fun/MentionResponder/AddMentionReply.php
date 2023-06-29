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

class AddMentionReply extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ADD_MENTION;
    }

    public function trigger(): string
    {
        return 'addreply';
    }
    public function __construct()
    {
        $this->description = __('bot.slash.addreply');

        $this->slashCommandOptions = [
            [
                'name' => 'group_id',
                'description' => 'Group ID',
                'type' => Option::INTEGER,
                'required' => true,
            ],
            [
                'name' => 'reply',
                'description' => 'Reply',
                'type' => Option::STRING,
                'required' => true,
            ],
        ];
        parent::__construct();
    }


    public function action(): MessageBuilder
    {
        $guildModel = \App\Models\Guild::get($this->guildId);
        $mentionGroup = MentionGroup::find($this->getOption('group_id'));
        if (!$mentionGroup) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.mention.no-group'));
        }
        $mentionGroup->replies()->save(new MentionReply(['reply' => $this->getOption('reply'), 'guild_id' => $guildModel->id]));
        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed($this->discord, __('bot.mention.added', ['group' => $mentionGroup->name, 'reply' => $this->getOption('reply')]));

    }
}
