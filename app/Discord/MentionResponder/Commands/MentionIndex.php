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
use Discord\Parts\Interactions\Interaction;
use Exception;

class MentionIndex extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::ADD_MENTION;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.mentionindex');
        $this->slashCommandOptions = [
            [
                'name' => 'group_id',
                'description' => __('bot.group'),
                'type' => Option::INTEGER,
                'required' => true,
            ],
        ];


        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
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

        return EmbedBuilder::create($this, __('bot.mention.title'), __('bot.mention.description', ['data' => $description]))->getEmbed();
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
