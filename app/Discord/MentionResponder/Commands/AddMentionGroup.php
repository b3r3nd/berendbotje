<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\MentionResponder\Actions\UpdateMentionGroupAction;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

class AddMentionGroup extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_MENTION_GROUP;
    }

    public function trigger(): string
    {
        return 'addgroup';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.addgroup');

        $this->slashCommandOptions = [
            [
                'name' => 'id',
                'description' => __('bot.role-or-user-id'),
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'group_type',
                'description' => __('bot.role-or-user'),
                'type' => Option::STRING,
                'required' => true,
                'choices' => [
                    ['name' => __('bot.role'), 'value' => 'has_role'],
                    ['name' => __('bot.user'), 'value' => 'has_user']
                ],
            ],
            [
                'name' => 'multiplier',
                'description' => __('bot.multiplier'),
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
        $group = MentionGroup::create(['name' => $this->getOption('id'), 'guild_id' => Guild::get($this->guildId)->id]);
        (new UpdateMentionGroupAction($group, $this->interaction->data->options))->execute();
        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed($this, __('bot.mentiongroup.added', ['group' => $this->getOption('id')]));
    }
}
