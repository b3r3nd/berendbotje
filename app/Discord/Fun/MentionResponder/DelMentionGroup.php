<?php

namespace App\Discord\Fun\MentionResponder;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\MentionGroup;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class DelMentionGroup extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_MENTION_GROUP;
    }

    public function trigger(): string
    {
        return 'delgroup';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delgroup');

        $this->slashCommandOptions = [
            [
                'name' => 'group_id',
                'description' => 'Group ID',
                'type' => Option::INTEGER,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $mentionGroup = MentionGroup::find($this->arguments[0]);

        if (!$mentionGroup) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.mentiongroup.not-found', ['id' => $this->getOption('group_id')]));
        }

        $mentionGroup->replies()->delete();
        $mentionGroup->delete();

        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();

        return EmbedFactory::successEmbed($this->discord, __('bot.mentiongroup.deleted'));
    }
}
