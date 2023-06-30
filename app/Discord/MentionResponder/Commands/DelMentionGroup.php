<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $mentionGroup = MentionGroup::find($this->getOption('group_id'));

        if (!$mentionGroup) {
            return EmbedFactory::failedEmbed($this, __('bot.mentiongroup.not-found', ['id' => $this->getOption('group_id')]));
        }

        $mentionGroup->replies()->delete();
        $mentionGroup->delete();

        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();

        return EmbedFactory::successEmbed($this, __('bot.mentiongroup.deleted'));
    }
}
