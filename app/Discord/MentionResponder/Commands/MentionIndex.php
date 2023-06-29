<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Levels\Helpers\Helper;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\MentionResponder\Models\MentionReply;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class MentionIndex extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::ADD_MENTION;
    }

    public function trigger(): string
    {
        return 'replies';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.mentionindex');
        $this->slashCommandOptions = [
            [
                'name' => 'group_id',
                'description' => 'Group',
                'type' => Option::INTEGER,
                'required' => true,
            ],
        ];


        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->perPage = 20;

        $groupId = $this->getOption('group_id');

        $mentionGroup = MentionGroup::byGuild($this->guildId)->where('id', $groupId)->first();
        $mentionReplies = MentionReply::byGuild($this->guildId)->where('group_id', $groupId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get();
        $this->total = MentionReply::byGuild($this->guildId)->where('group_id', $groupId)->count();

        $description = Helper::getGroupName($mentionGroup);
        foreach ($mentionReplies as $mentionReply) {
            $description .= "** {$mentionReply->id} ** - {$mentionReply->reply} \n";
        }

        return EmbedBuilder::create($this->discord)
            ->setTitle(__('bot.mention.title'))
            ->setFooter(__('bot.mention.footer'))
            ->setDescription(__('bot.mention.description', ['data' => $description]))
            ->getEmbed();
    }
}