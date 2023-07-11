<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\MentionResponder\Actions\UpdateMentionGroupAction;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class UpdateMentionGroup extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_MENTION_GROUP;
    }

    public function trigger(): string
    {
        return 'edit';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.updategroup');

        $this->slashCommandOptions = [
            [
                'name' => 'id',
                'description' => __('bot.role-or-user-id'),
                'type' => Option::INTEGER,
                'required' => true,
            ],
            [
                'name' => 'group_type',
                'description' => __('bot.role-or-user'),
                'type' => Option::STRING,
                'required' => true,
                'choices' => [
                    ['name' => "Role", 'value' => 'has_role'],
                    ['name' => "User", 'value' => 'has_user']
                ],
            ],
            [
                'name' => 'multiplier',
                'description' => __('bot.multiplier'),
                'type' => Option::INTEGER,
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
        $group = MentionGroup::find($this->getOption('id'));
        if (!$group) {
            return EmbedFactory::failedEmbed($this, __('bot.mentiongroup.notexist', ['group' => $this->getOption('id')]));
        }

        (new UpdateMentionGroupAction($group, $this->interaction->data->options))->execute();

        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed($this, __('bot.mentiongroup.updated', ['group' => $this->getOption('id')]));
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
