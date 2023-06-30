<?php

namespace App\Discord\Help\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\Components\Option;
use Discord\Builders\Components\SelectMenu;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection;
use Discord\Parts\Interactions\Interaction;
use Exception;

class Help extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return "help";
    }

    public function __construct()
    {
        $this->description = "Help and information";
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $select = SelectMenu::new()
            ->addOption(Option::new("Home"))
            ->addOption(Option::new("Roles"))
            ->addOption(Option::new("Moderation"))
            ->addOption(Option::new("Levels"))
            ->addOption(Option::new("Fun"))
            ->addOption(Option::new("MentionResponder"))
            ->addOption(Option::new("Settings"))
            ->addOption(Option::new("Logs"));

        $select->setListener(function (Interaction $interaction, Collection $options) {
            $option = $options->first();
            $roleCommands = [];

            $embedBuilder = EmbedBuilder::create($this, $option->getLabel());
            $embedBuilder->getEmbed()->setColor(2303786);

            if ($option->getLabel() === "Home") {
                $embedBuilder = $this->getGeneralPage();
            }

            if ($option->getLabel() === "Roles") {
                $embedBuilder->setDescription("The main Admin role cannot be deleted, permissions cannot be removed from the admin role and the role cannot be removed from the owner of the guild.");
                $roleCommands = [
                    ['cmd' => 'roles', 'usage' => '', 'desc' => 'Overview of all roles and their permissions'],
                    ['cmd' => 'permissions', 'usage' => '', 'desc' => 'Overview of all permissions'],
                    ['cmd' => 'role', 'usage' => '`<user_mention>`', 'desc' => 'See your roles or from another user'],
                    ['cmd' => 'addrole', 'usage' => '`<role_name>`', 'desc' => 'Add a new role'],
                    ['cmd' => 'delrole', 'usage' => '`<role_name>`', 'desc' => 'Delete a rol'],
                    ['cmd' => 'addperm', 'usage' => '`<role_name>` `<perm_name>`', 'desc' => 'Add permission(s) to a role'],
                    ['cmd' => 'delperm', 'usage' => '`<role_name>` `<perm_name>`', 'desc' => 'Remove permission(s) from a role'],
                    ['cmd' => 'adduser', 'usage' => '`<user_mention>` `<role_name>`', 'desc' => 'Add user to the given role'],
                    ['cmd' => 'deluser', 'usage' => '`<user_mention>` `<role_name>`', 'desc' => ' Remove user from given role'],
                ];
            }
            if ($option->getLabel() === "Moderation") {
                $embedBuilder->setDescription("Timeouts are automatically detected and saved, bans and kicks are only counted.");
                $roleCommands = [
                    ['cmd' => 'timeouts', 'usage' => '`<user_mention>`', 'desc' => 'Show given timeout history or from a specific user'],
                    ['cmd' => 'modstats', 'usage' => '', 'desc' => 'Show moderator statistics'],
                    ['cmd' => 'config', 'usage' => '', 'desc' => 'See server configuration'],
                    ['cmd' => 'set', 'usage' => '`<setting_key>` `<new_value>`', 'desc' => 'Update server setting'],
                    ['cmd' => 'channels', 'usage' => '', 'desc' => 'Shows a list of channels with their configured flags'],
                    ['cmd' => 'markchannel', 'usage' => '`<channel>` `<flag>`', 'desc' => 'Mark a channel with one of the available flags'],
                    ['cmd' => 'unmarkchannel', 'usage' => '`<channel>` `<flag>`', 'desc' => 'Unmark a channel with one of the available flags'],
                    ['cmd' => 'commands', 'usage' => '', 'desc' => 'Show list of custom commands'],
                    ['cmd' => 'addcmd', 'usage' => '`<command>` `<response>`', 'desc' => 'Remove a custom command'],
                    ['cmd' => 'delcmd', 'usage' => '`<command>`', 'desc' => ''],
                ];
                $embedBuilder->getEmbed()->addField(
                    ['name' => 'Channel flags', 'value' => "`no_xp` Users gain no XP in this channel \n `media_only` Channel allows only media and URLS. \n `no_stickers` Stickers will be removed from the chat \n `no_log` Message logging is disabled for this channel"],
                );
            }

            if ($option->getLabel() === "Levels") {
                $embedBuilder->setDescription("The bot counts messages send by users and gives xp for each message, it also detects users in voice who are not muted and gives XP for time spend in voice. The amount of XP gained by each message, time spend in voice and the cooldown between messages can be changed with the `config` command see `help settings` for more info.\n Role rewards for users are synced whenever they send a message to the server. When removing or adding XP from users their roles will persist until they send a message.");
                $roleCommands = [
                    ['cmd' => 'leaderboard', 'usage' => '', 'desc' => 'Show the leaderboard'],
                    ['cmd' => 'rank', 'usage' => '`<user_mention>`', 'desc' => 'Show level, xp and messages for you or another user'],
                    ['cmd' => 'givexp', 'usage' => '`<user_mention>` `<xp_amount>`', 'desc' => 'Give user xp'],
                    ['cmd' => 'removexp', 'usage' => '`<user_mention>` `<xp_amount>`', 'desc' => 'Remove user xp'],
                    ['cmd' => 'resetxp', 'usage' => '`<user_mention>`', 'desc' => 'Reset XP for user'],
                    ['cmd' => 'rewards', 'usage' => '', 'desc' => 'Show the role rewards for different levels'],
                    ['cmd' => 'addreward', 'usage' => '`<level>` `<role_id>`', 'desc' => 'Add a role reward to a level'],
                    ['cmd' => 'delreward', 'usage' => '`<level>`', 'desc' => 'Delete role rewards from this level'],
                ];
            }

            if ($option->getLabel() === "Fun") {
                $embedBuilder->setDescription("Some fun commands");
                $roleCommands = [
                    ['cmd' => 'cringecounter', 'usage' => '', 'desc' => 'Show who is most cringe..'],
                    ['cmd' => 'addcringe', 'usage' => '`<user_mention>`', 'desc' => 'Increase cringe counter'],
                    ['cmd' => 'delcringe', 'usage' => '`<user_mention>`', 'desc' => 'Decrease cringe counter'],
                    ['cmd' => 'resetcringe', 'usage' => '`<user_mention>`', 'desc' => 'Reset cringe counter'],
                    ['cmd' => 'reactions', 'usage' => '', 'desc' => 'Show list custom reactions'],
                    ['cmd' => 'addreaction', 'usage' => '`<trigger>` `<emoji>`', 'desc' => 'Add new reactions'],
                    ['cmd' => 'delreaction', 'usage' => '`<trigger>`', 'desc' => 'Delete a reaction'],
                    ['cmd' => 'bumpstats', 'usage' => '`<date_range>`', 'desc' => 'Show bumper elites leaderboard'],
                    ['cmd' => 'emotes', 'usage' => '', 'desc' => 'Shows scoreboard of most used emotes'],
                    ['cmd' => 'leaderboard', 'usage' => '', 'desc' => 'Shows leaderboard based on messages and xp gained'],
                    ['cmd' => 'rank', 'usage' => '', 'desc' => 'Shows your current rank, message counter and XP'],
                    ['cmd' => 'urb', 'usage' => '`<search_term>`', 'desc' => 'Search something on urban dictionary'],
                    ['cmd' => '8ball', 'usage' => '`<question>`', 'desc' => 'Ask the magic 8ball'],
                    ['cmd' => 'ask', 'usage' => '`<question>`', 'desc' => 'Yes? No? Hmm..?'],
                    ['cmd' => 'image', 'usage' => '`<term>`', 'desc' => 'Generate an Image using OpenAI'],
                ];
            }

            if ($option->getLabel() === "Settings") {
                $embedBuilder->setDescription("All setting values are numeric, for booleans 0 = false, 1 = true. (`duration_in_voice` / `xp_voice_cooldown`) * `xp_voice_count` = Amount of XP gained, calculated on voice disconnect.");
                $roleCommands = [
                    ['cmd' => 'config', 'usage' => '', 'desc' => 'Show all config settings'],
                    ['cmd' => 'set', 'usage' => '`<setting_key>` `<setting_value>`', 'desc' => 'Update a setting'],
                ];
                $embedBuilder->getEmbed()->addField(
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
            }

            if ($option->getLabel() === "Logs") {
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

                $embedBuilder->setDescription($desc);
                $roleCommands = [
                    ['cmd' => 'logconfig', 'usage' => '', 'desc' => 'See the log config'],
                    ['cmd' => 'logset', 'usage' => '`<key>` `<value>`', 'desc' => 'Update a value in the log config'],
                ];
            }

            if ($option->getLabel() === "MentionResponder") {
                $embedBuilder->setDescription("Small funny feature, when you tag the bot you will get a random reply from a list of mention replies. There are default replies, but you can also add your own replies based on certain roles in the server.");
                $roleCommands = [
                    ['cmd' => 'replies', 'usage' => '`<group_id>`', 'desc' => 'Show all replies or filter by group'],
                    ['cmd' => 'groups', 'usage' => '', 'desc' => 'Show all groups'],
                    ['cmd' => 'addgroup', 'usage' => '`<discord_role>` `<user/role>` `<multiplier>`', 'desc' => 'Add a group'],
                    ['cmd' => 'updategroup', 'usage' => '`<id>` `<user/role>` `<multiplier>`', 'desc' => 'Update a group'],
                    ['cmd' => 'delgroup', 'usage' => '`<group_id>`', 'desc' => 'Delete a group'],
                    ['cmd' => 'addreply', 'usage' => '`<group_id>` `<reply_line>`', 'desc' => 'Add a reply to a group'],
                    ['cmd' => 'delreply', 'usage' => '`<reply_id>`', 'desc' => 'Remove a reply'],
                ];
            }

            foreach ($roleCommands as $commandData) {
                $cmd = $commandData['cmd'];
                $command = $this->discord->application->commands->get('name', $cmd);
                $commandName = $command ? "</{$cmd}:{$command->id}>" : "/{$cmd}";
                $embedBuilder->getEmbed()->addField(['name' => $commandName . ' ' . $commandData['usage'], 'value' => $commandData['desc']]);
            }
            $interaction->message->edit(MessageBuilder::new()->addEmbed($embedBuilder->getEmbed()));
        }, $this->discord);

        $embedBuilder = $this->getGeneralPage();
        return MessageBuilder::new()->addComponent($select)->addEmbed($embedBuilder->getEmbed());
    }

    /**
     * @return EmbedBuilder
     * @throws Exception
     */
    public function getGeneralPage(): EmbedBuilder
    {
        $embedBuilder = EmbedBuilder::create($this, __('bot.help.title'), "Bot uses **only** slash commands. For more information see https://github.com/b3r3nd/berendbotje.");
        $embedBuilder->getEmbed()->setColor(2303786);
        $embedBuilder->getEmbed()->addField(
            ['name' => 'Roles', 'value' => 'Managing roles and permissions'],
            ['name' => 'Settings', 'value' => 'Explains all the settings and values'],
            ['name' => 'Moderation', 'value' => 'Moderator actions'],
            ['name' => 'Levels', 'value' => 'Levels and XP'],
            ['name' => 'Logs', 'value' => 'Which events are logged'],
            ['name' => 'MentionResponder', 'value' => 'Manage the responses when you tag the bot'],
            ['name' => 'Fun', 'value' => 'Fun commands'],
        );

        return $embedBuilder;
    }
}
