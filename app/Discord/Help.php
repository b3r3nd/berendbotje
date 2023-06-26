<?php

namespace App\Discord;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class Help extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'help';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.help');
        $this->slashCommandOptions = [
            [
                'name' => 'section',
                'description' => 'Section',
                'type' => Option::STRING,
                'required' => false,
                'choices' => [
                    ['name' => 'Roles', 'value' => 'roles'],
                    ['name' => 'Moderation', 'value' => 'moderation'],
                    ['name' => 'Levels', 'value' => 'levels'],
                    ['name' => 'Fun', 'value' => 'fun'],
                    ['name' => 'MentionResponder', 'value' => 'mentions'],
                    ['name' => 'Settings', 'value' => 'settings'],
                    ['name' => 'Logs', 'value' => 'logs'],
                ]
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.help.title'))
            ->setFooter(__('bot.help.footer'));

        if (isset($this->arguments[0])) {
            if (strtolower($this->arguments[0]) === 'roles') {
                $desc = "The main Admin role cannot be deleted, permissions cannot be removed from the admin role and the role cannot be removed from the owner of the guild.";
                $embedBuilder->getEmbed()->addField(
                    ['name' => 'roles', 'value' => 'Overview of all roles and their permissions'],
                    ['name' => 'permissions', 'value' => 'Overview of all permissions'],
                    ['name' => 'role  `<user_mention>`', 'value' => 'See your roles or from another user'],
                    ['name' => 'addrole `<role_name>`', 'value' => 'Add a new role'],
                    ['name' => 'delrole `<role_name>`', 'value' => 'Delete a rol'],
                    ['name' => 'addperm `<role_name>` `<perm_name>`', 'value' => 'Add permission(s) to a role'],
                    ['name' => 'delperm `<role_name>` `<perm_name>`', 'value' => 'Remove permission(s) from a role'],
                    ['name' => 'adduser `<user_mention>` `<role_name>`', 'value' => 'Add user to the given role'],
                    ['name' => 'deluser `<user_mention>` `<role_name>`', 'value' => ' Remove user from given role'],
                );


                $embedBuilder->setDescription($desc)->setTitle("Roles and Permissions");
            } elseif (strtolower($this->arguments[0]) === 'moderation') {
                $desc = "Timeouts are automatically detected and saved, bans and kicks are only counted.";
                $embedBuilder->getEmbed()->addField(
                    ['name' => 'timeouts `<user_mention>`', 'value' => 'Show given timeout history or from a specific user'],
                    ['name' => 'modstats', 'value' => 'Show moderator statistics'],
                    ['name' => 'config', 'value' => 'See server configuration'],
                    ['name' => 'set `<setting_key>` `<new_value>`', 'value' => ' Update server setting'],
                    ['name' => 'channels', 'value' => 'Shows a list of channels with their configured flags'],
                    ['name' => 'markchannel `<channel>` `<flag>`', 'value' => 'Mark a channel with one of the available flags'],
                    ['name' => 'unmarkchannel `<channel>` `<flag>`', 'value' => ' Unmark a channel with one of the available flags'],
                    ['name' => 'commands', 'value' => ' Show list of custom commands'],
                    ['name' => 'addcmd  `<command>` `<response>`', 'value' => 'Add a custom command'],
                    ['name' => 'delcmd  `<command>`', 'value' => 'Remove a custom command'],
                    ['name' => 'Channel flags', 'value' => "`no_xp` Users gain no XP in this channel \n `media_only` Channel allows only media and URLS. \n `no_stickers` Stickers will be removed from the chat \n `no_log` Message logging is disabled for this channel"],
                );
                $embedBuilder->setDescription($desc)->setTitle("Moderation");
            } elseif (strtolower($this->arguments[0]) === 'levels') {
                $desc = "The bot counts messages send by users and gives xp for each message, it also detects users in voice who are not muted and gives XP for time spend in voice. The amount of XP gained by each message, time spend in voice and the cooldown between messages can be changed with the `config` command see `help settings` for more info.\n Role rewards for users are synced whenever they send a message to the server. When removing or adding XP from users their roles will persist until they send a message.";
                $embedBuilder->getEmbed()->addField(
                    ['name' => 'leaderboard', 'value' => 'Show the leaderboard with highest ranking members at the top'],
                    ['name' => 'rank', 'value' => 'Show your own level, xp, messages and time in voice'],
                    ['name' => 'rank `<user_mention>`', 'value' => 'Show level, xp, messages and time in voice for another user'],
                    ['name' => 'givexp `<user_mention>` `<xp_amount>`', 'value' => 'Give user xp'],
                    ['name' => 'removexp `<user_mention> `<xp_amount>`', 'value' => 'Remove user xp'],
                    ['name' => 'resetxp `<user_mention>`', 'value' => 'Reset XP for user'],
                    ['name' => 'rewards', 'value' => 'Show the role rewards for different levels'],
                    ['name' => 'addreward `<level>` `<role_id>`', 'value' => 'Add a role reward to a level'],
                    ['name' => 'delreward `<level>`', 'value' => 'Delete role rewards from this level'],
                );

                $embedBuilder->setDescription($desc)->setTitle("Levels and XP");
            } elseif (strtolower($this->arguments[0]) === 'fun') {
                $embedBuilder->getEmbed()->addField(
                    ['name' => 'cringecounter', 'value' => 'Show who is most cringe..'],
                    ['name' => 'addcringe `<user_mention>`', 'value' => 'Increase cringe counter'],
                    ['name' => 'delcringe `<user_mention>`', 'value' => 'Decrease cringe counter'],
                    ['name' => 'resetcringe `<user_mention>`', 'value' => 'Reset cringe counter'],
                    ['name' => 'reactions', 'value' => ' Show list custom reactions'],
                    ['name' => 'addreaction `<trigger>` `<emoji>`', 'value' => 'Add new reactions'],
                    ['name' => 'delreaction `<trigger>`', 'value' => 'Delete a reaction'],
                    ['name' => 'bumpstats `<date_range>`', 'value' => 'Show bumper elites leaderboard'],
                    ['name' => 'emotes', 'value' => 'Shows scoreboard of most used emotes'],
                    ['name' => 'leaderboard', 'value' => ' Shows leaderboard based on messages and xp gained'],
                    ['name' => 'rank', 'value' => ' Shows your current rank, message counter and XP'],
                    ['name' => 'urb `<search_term>` ', 'value' => ' Search something on urban dictionary'],
                    ['name' => '8ball `<question>`', 'value' => 'Ask the magic 8ball'],
                    ['name' => 'ask `<question>`', 'value' => 'Yes? No? Hmm..?'],
                    ['name' => 'image `<term>`', 'value' => 'Generate an Image using OpenAI'],
                );

                $embedBuilder->setDescription("Some fun commands")->setTitle("Fun commands");
            } elseif (strtolower($this->arguments[0]) === 'settings') {
                $desc = "All setting values are numeric, for booleans 0 = false, 1 = true. (`duration_in_voice` / `xp_voice_cooldown`) * `xp_voice_count` = Amount of XP gained, calculated on voice disconnect.";
                $embedBuilder->getEmbed()->addField(
                    ['name' => 'config', 'value' => 'Show all config settings'],
                    ['name' => 'set `<setting_key>` `<setting_value>`', 'value' => 'Update a setting'],
                    ['name' => 'xp_count', 'value' => ' Amount of xp you gain per message'],
                    ['name' => 'xp_cooldown', 'value' => 'Cooldown between messages before gain `xp_count` again.'],
                    ['name' => 'xp__voice_count', 'value' => ' Amount of xp you gain in voice'],
                    ['name' => 'xp_voice_cooldown', 'value' => 'Over how much time the XP is given.'],
                    ['name' => 'enable_xp', 'value' => 'Enable the message XP system'],
                    ['name' => 'enable_voice_xp', 'value' => 'Enable the voice XP system'],
                    ['name' => 'enable_emote_counter', 'value' => 'Enable emote counter'],
                    ['name' => 'enable_role_rewards', 'value' => 'Enable role rewards'],
                    ['name' => 'enable_bump_counter', 'value' => 'Enable bump counter'],
                    ['name' => 'enable_reactions', 'value' => 'Enable custom reactions'],
                    ['name' => 'enable_commands', 'value' => 'Enable custom commands'],
                    ['name' => 'enable_logging', 'value' => 'Enable general logs'],
                    ['name' => 'log_channel', 'value' => 'ID of the log channel'],
                    ['name' => 'enable_bump_reminder', 'value' => 'Enable the bump reminder'],
                    ['name' => 'bump_reminder_role', 'value' => 'What role should be tagged for the bump reminder'],
                    ['name' => 'bump_channel', 'value' => 'Channel where to tag the role'],
                    ['name' => 'enable_mention_responder', 'value' => ' Enable the mention responder'],
                    ['name' => 'enable_qotd_reminder', 'value' => 'Enable the role mention in set question of the day channel'],
                    ['name' => 'qotd_channel', 'value' => 'Channel to tag qotd role'],
                    ['name' => 'qotd_role', 'value' => 'ole to tag in qotd channel'],
                );
                $embedBuilder->setDescription($desc)->setTitle("Settings");
            } elseif (strtolower($this->arguments[0]) === 'logs') {
                $desc = "General Logging commands and events \n
                Joined server
                Left server
                Kicked from server
                Banned from server
                Unbanned from server
                Received timeout
                Joined voice call
                Left voice call
                Switched voice call
                Muted in voice by moderator
                Unmuted in voice by moderator
                Updated username (old and new username)
                Message updated (show old and new message)
                Message deleted (show deleted message)
                Invite created
                Invite removed
                Started streaming in voice
                Stopped streaming in voice
                Enabled his webcam in voice
                Disabled his webcam in voice";

                $embedBuilder->getEmbed()->addField(
                    ['name' => 'logconfig', 'value' => 'See the log config'],
                    ['name' => 'logset `<key>` `<value>`', 'value' => 'Update a value in the log config'],
                );

                $embedBuilder->setDescription($desc)->setTitle("Logs");
            } elseif (strtolower($this->arguments[0]) === 'mentions') {
                $desc = "
                Small funny feature, when you tag the bot you will get a random reply from a list of mention replies. There are default replies, but you can also add your own replies based on certain roles in the server.";
                $embedBuilder->getEmbed()->addField(
                    ['name' => 'replies', 'value' => 'Show all replies grouped by available categories'],
                    ['name' => 'replies `<group_id>`', 'value' => 'Show all replies for a single group'],
                    ['name' => 'groups', 'value' => 'Show all groups'],
                    ['name' => 'addgroup `<discord_role>` `<user/role>` `<multiplier>`', 'value' => 'Add a group'],
                    ['name' => 'updategroup `<id>` `<user/role>` `<multiplier>`', 'value' => 'Update a group'],
                    ['name' => 'delgroup `<group_id>`', 'value' => 'Delete a group and its replies (!!!)'],
                    ['name' => 'addreply `<group_id>` `<reply_line>`', 'value' => 'Add a reply to a group'],
                    ['name' => 'delreply `<reply_id>`', 'value' => 'Delete a reply'],
                );

                $embedBuilder->setDescription($desc)->setTitle("Mention Responder");
            }
            return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());

        }

        $embedBuilder->getEmbed()->addField(
            ['name' => 'Help Files', 'value' => "Commands are only available as slash commands `/`.\n"],
            ['name' => 'Roles', 'value' => 'Managing roles and permissions'],
            ['name' => 'Settings', 'value' => 'Explains all the settings and values'],
            ['name' => 'Moderation', 'value' => 'Moderator actions'],
            ['name' => 'Levels', 'value' => 'Levels and XP'],
            ['name' => 'Logs', 'value' => 'Which events are logged'],
            ['name' => 'MentionResponder', 'value' => 'Manage the responses when you tag the bot'],
            ['name' => 'Fun', 'value' => 'Fun commands'],
        );
        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}

