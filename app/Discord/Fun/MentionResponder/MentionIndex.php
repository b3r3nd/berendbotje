<?php

namespace App\Discord\Fun\MentionResponder;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Models\MentionGroup;
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
                'required' => false,
            ],
        ];


        parent::__construct();
    }

    public function getEmbed(): Embed
    {
        $this->perPage = 1;

        if (isset($this->arguments[0])) {
            $mentionGroups = MentionGroup::byGuild($this->guildId)->where('id', $this->arguments[0])->get();
            $this->total = MentionGroup::byGuild($this->guildId)->where('id', $this->arguments[0])->count();
        } else {
            $mentionGroups = MentionGroup::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get();
            $this->total = MentionGroup::byGuild($this->guildId)->count();
        }


        $description = "";
        foreach ($mentionGroups as $mentionGroup) {
            if (is_numeric($mentionGroup->name)) {
                $description .= "{$mentionGroup->id} - **<@&{$mentionGroup->name}>** \n";
            } else {
                $description .= "{$mentionGroup->id} - **{$mentionGroup->name}** \n";
            }


            foreach ($mentionGroup->replies as $mentionReply) {
                $description .= "** {$mentionReply->id} ** - {$mentionReply->reply} \n";
            }
            $description .= "\n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.mention.title'))
            ->setFooter(__('bot.mention.footer'))
            ->setDescription(__('bot.mention.description', ['data' => $description]))
            ->getEmbed();
    }
}
