<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class DelMentionGroup extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_MENTION_GROUP;
    }

    public function trigger(): string
    {
        return 'delete';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.delgroup');

        $this->slashCommandOptions = [
            [
                'name' => 'group_id',
                'description' => __('bot.group-id'),
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

        if(!$mentionGroup->is_custom) {
            return EmbedFactory::failedEmbed($this, __('bot.mentiongroup.delete-default', ['id' => $this->getOption('group_id')]));
        }

        $mentionGroup->replies()->delete();
        $mentionGroup->delete();

        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();

        return EmbedFactory::successEmbed($this, __('bot.mentiongroup.deleted'));
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
