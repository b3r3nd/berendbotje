<?php

namespace App\Discord;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\Components\Option;
use Discord\Builders\Components\SelectMenu;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;
use http\Message;

class Help extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return "general";
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
        $messageBuilder = MessageBuilder::new();
        $select = SelectMenu::new()
            ->addOption(Option::new("Home"))
            ->addOption(Option::new("Roles"))
            ->addOption(Option::new("Moderation"))
            ->addOption(Option::new("Levels"))
            ->addOption(Option::new("Fun"))
            ->addOption(Option::new("MentionResponder"))
            ->addOption(Option::new("Settings"))
            ->addOption(Option::new("User Settings"))
            ->addOption(Option::new("Logs"));

        $select->setListener(function (Interaction $interaction, Collection $options) use ($select) {
            $option = $options->first();
            $messageBuilder = MessageBuilder::new();
            $embedBuilder = EmbedBuilder::create($this, $option->getLabel());
            $embedBuilder->getEmbed()->setColor(2303786);
            $interaction->message->components->clear();
            $interaction->message->edit(MessageBuilder::new()->addComponent($select));

            if ($option->getLabel() === "Home") {
                $embedBuilder = $this->getGeneralPage();
            }
            if ($option->getLabel() === "Fun") {
                $embedBuilder = $this->funReactionsPage($embedBuilder);
                $messageBuilder->addComponent($select)->addComponent($this->funSubSelect());
            }
            if ($option->getLabel() === "Roles") {
                $embedBuilder = $this->rolesPage($embedBuilder);
            }
            if ($option->getLabel() === "Moderation") {
                $embedBuilder = $this->moderationPage($embedBuilder);
            }
            if ($option->getLabel() === "Levels") {
                $embedBuilder = $this->levelsPage($embedBuilder);
            }
            if ($option->getLabel() === "Settings") {
                $embedBuilder = $this->settingsPage($embedBuilder);
            }
            if ($option->getLabel() === 'User Settings') {
                $embedBuilder = $this->userSettingsPage($embedBuilder);
            }
            if ($option->getLabel() === "Logs") {
                $embedBuilder = $this->logsPage($embedBuilder);
            }
            if ($option->getLabel() === "MentionResponder") {
                $embedBuilder = $this->mentionResponder($embedBuilder);
            }
            $interaction->message->edit($messageBuilder->addEmbed($embedBuilder->getEmbed()));
        }, $this->discord);

        $embedBuilder = $this->getGeneralPage();
        return $messageBuilder->addComponent($select)->addEmbed($embedBuilder->getEmbed());
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }


    /**
     * Makes sure commands are clickable!
     *
     * @param array $roleCommands
     * @param EmbedBuilder $embedBuilder
     * @return void
     */
    public function addCommands(array $roleCommands, EmbedBuilder $embedBuilder): void
    {
        foreach ($roleCommands as $commandData) {
            $cmd = $commandData['cmd'];
            $command = $this->discord->application->commands->get('name', $cmd);
            $commandName = $command ? "</{$cmd}:{$command->id}>" : "/{$cmd}";
            $embedBuilder->getEmbed()->addField(['name' => $commandName . ' ' . $commandData['usage'], 'value' => $commandData['desc']]);
        }
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
            ['name' => 'User Settings', 'value' => 'All user settings'],
            ['name' => 'Moderation', 'value' => 'Moderator actions'],
            ['name' => 'Levels', 'value' => 'Levels and XP'],
            ['name' => 'Logs', 'value' => 'Which events are logged'],
            ['name' => 'MentionResponder', 'value' => 'Manage the responses when you tag the bot'],
            ['name' => 'Fun', 'value' => 'Fun commands'],
        );
        return $embedBuilder;
    }

    /**
     * @return SelectMenu
     */
    public function funSubSelect(): SelectMenu
    {
        $subSelect = SelectMenu::new()
            ->addOption(Option::new("Reactions"))
            ->addOption(Option::new("Cringe Counter"))
            ->addOption(Option::new("Bump Stats"))
            ->addOption(Option::new("Custom Commands"))
            ->addOption(Option::new("Extra"));

        $subSelect->setListener(function (Interaction $interaction, Collection $options) {
            $option = $options->first();
            $embedBuilder = EmbedBuilder::create($this, $option->getLabel());
            $embedBuilder->setDescription($option->getLabel());

            if ($option->getLabel() === 'Reactions') {
                $embedBuilder = $this->funReactionsPage($embedBuilder);
            }
            if ($option->getLabel() === 'Cringe Counter') {
                $embedBuilder = $this->funCringePage($embedBuilder);
            }
            if ($option->getLabel() === 'Bump Stats') {
                $embedBuilder = $this->funBumpPage($embedBuilder);
            }
            if ($option->getLabel() === 'Extra') {
                $embedBuilder = $this->funExtraPage($embedBuilder);
            }
            if ($option->getLabel() === 'Custom Commands') {
                $embedBuilder = $this->funCommandsPage($embedBuilder);
            }

            $interaction->message->edit(MessageBuilder::new()->addEmbed($embedBuilder->getEmbed()));
        }, $this->discord);

        return $subSelect;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function userSettingsPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("You can change settings on a guild basis. For now there is only a single setting: \n\n **no_role_rewards** - Disable gaining role rewards based on levels");
        $this->addCommands([
            ['cmd' => 'userconfig', 'usage' => '', 'desc' => 'Shows your custom user settings'],
            ['cmd' => 'userset', 'usage' => '`<setting_key>` `<new_value>`', 'desc' => 'Change custom user settings'],
        ], $embedBuilder);
        return $embedBuilder;
    }


    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function funExtraPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("List of other small fun commands");
        $this->addCommands([
            ['cmd' => 'emotes', 'usage' => '', 'desc' => 'Shows scoreboard of most used emotes'],
            ['cmd' => 'leaderboard', 'usage' => '', 'desc' => 'Shows leaderboard based on messages and xp gained'],
            ['cmd' => 'rank', 'usage' => '', 'desc' => 'Shows your current rank, message counter and XP'],
            ['cmd' => 'urb', 'usage' => '`<search_term>`', 'desc' => 'Search something on urban dictionary'],
            ['cmd' => '8ball', 'usage' => '`<question>`', 'desc' => 'Ask the magic 8ball'],
            ['cmd' => 'ask', 'usage' => '`<question>`', 'desc' => 'Yes? No? Hmm..?'],
            ['cmd' => 'image', 'usage' => '`<term>`', 'desc' => 'Generate an Image using OpenAI'],
        ], $embedBuilder);
        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function funCringePage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("Who is most cringe?");
        $this->addCommands([
            ['cmd' => 'cringecounter', 'usage' => '', 'desc' => 'Show who is most cringe..'],
            ['cmd' => 'addcringe', 'usage' => '`<user_mention>`', 'desc' => 'Increase cringe counter'],
            ['cmd' => 'delcringe', 'usage' => '`<user_mention>`', 'desc' => 'Decrease cringe counter'],
            ['cmd' => 'resetcringe', 'usage' => '`<user_mention>`', 'desc' => 'Reset cringe counter'],
        ], $embedBuilder);
        return $embedBuilder;
    }


    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function funBumpPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("The bot keeps track of how often people bump our server, and shows some statistics");
        $this->addCommands([
            ['cmd' => 'bumpstats', 'usage' => '`<date_range>`', 'desc' => 'Show bumper elites leaderboard'],
        ], $embedBuilder);
        return $embedBuilder;
    }


    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function funCommandsPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("You can add custom message commands, make sure to include the trigger");
        $this->addCommands([
            ['cmd' => 'commands', 'usage' => '', 'desc' => 'Show list of custom commands'],
            ['cmd' => 'addcmd', 'usage' => '`<command>` `<response>`', 'desc' => 'Remove a custom command'],
            ['cmd' => 'delcmd', 'usage' => '`<command>`', 'desc' => ''],
        ], $embedBuilder);

        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function funReactionsPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("You can add reactions to words, when the bot detects those words, it will react!");
        $this->addCommands([
            ['cmd' => 'reactions', 'usage' => '', 'desc' => 'Show list custom reactions'],
            ['cmd' => 'addreaction', 'usage' => '`<trigger>` `<emoji>`', 'desc' => 'Add new reactions'],
            ['cmd' => 'delreaction', 'usage' => '`<trigger>`', 'desc' => 'Delete a reaction'],
        ], $embedBuilder);
        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function rolesPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("The main Admin role cannot be deleted, permissions cannot be removed from the admin role and the role cannot be removed from the owner of the guild.");
        $this->addCommands([
            ['cmd' => 'roles', 'usage' => '', 'desc' => 'Overview of all roles and their permissions'],
            ['cmd' => 'permissions', 'usage' => '', 'desc' => 'Overview of all permissions'],
            ['cmd' => 'role', 'usage' => '`<user_mention>`', 'desc' => 'See your roles or from another user'],
            ['cmd' => 'addrole', 'usage' => '`<role_name>`', 'desc' => 'Add a new role'],
            ['cmd' => 'delrole', 'usage' => '`<role_name>`', 'desc' => 'Delete a rol'],
            ['cmd' => 'addperm', 'usage' => '`<role_name>` `<perm_name>`', 'desc' => 'Add permission(s) to a role'],
            ['cmd' => 'delperm', 'usage' => '`<role_name>` `<perm_name>`', 'desc' => 'Remove permission(s) from a role'],
            ['cmd' => 'adduser', 'usage' => '`<user_mention>` `<role_name>`', 'desc' => 'Add user to the given role'],
            ['cmd' => 'deluser', 'usage' => '`<user_mention>` `<role_name>`', 'desc' => ' Remove user from given role'],
        ], $embedBuilder);
        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function moderationPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("Timeouts are automatically detected and saved, bans and kicks are only counted.");
        $this->addCommands([
            ['cmd' => 'timeouts', 'usage' => '`<user_mention>`', 'desc' => 'Show given timeout history or from a specific user'],
            ['cmd' => 'edittimeout', 'usage' => '`<timeout_id>` `<reason>`', 'desc' => 'Update the reason for a given timeout'],
            ['cmd' => 'removetimeout', 'usage' => '`<timeout_id>`', 'desc' => 'Remove a timeout from the log only'],
            ['cmd' => 'unblock', 'usage' => '`<user_mention>`', 'desc' => 'Remove someone from the blacklist'],
            ['cmd' => 'modstats', 'usage' => '', 'desc' => 'Show moderator statistics'],
            ['cmd' => 'config', 'usage' => '', 'desc' => 'See server configuration'],
            ['cmd' => 'set', 'usage' => '`<setting_key>` `<new_value>`', 'desc' => 'Update server setting'],
            ['cmd' => 'channels', 'usage' => '', 'desc' => 'Shows a list of channels with their configured flags'],
            ['cmd' => 'markchannel', 'usage' => '`<channel>` `<flag>`', 'desc' => 'Mark a channel with one of the available flags'],
            ['cmd' => 'unmarkchannel', 'usage' => '`<channel>` `<flag>`', 'desc' => 'Unmark a channel with one of the available flags'],
            ['cmd' => 'blacklist', 'usage' => '', 'desc' => 'Shows the abusers on the blacklist'],
            ['cmd' => 'block', 'usage' => '`<user_mention>`', 'desc' => 'Add someone to the blacklist'],
            ['cmd' => 'unblock', 'usage' => '`<user_mention>`', 'desc' => 'Remove someone from the blacklist'],
        ], $embedBuilder);
        $embedBuilder->getEmbed()->addField(
            ['name' => 'Channel flags', 'value' => "`no_xp` Users gain no XP in this channel \n `media_only` Channel allows only media and URLS. \n `no_stickers` Stickers will be removed from the chat \n `no_log` Message logging is disabled for this channel"],
        );
        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function levelsPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $configCmd = $this->discord->application->commands->get('name', 'config') ? "</config:{$this->discord->application->commands->get('name', 'config')->id}>" : "`/config`";
        $helpCmd = $this->discord->application->commands->get('name', 'help') ? "</help:{$this->discord->application->commands->get('name', 'help')->id}>" : "`/help`";
        $embedBuilder->setDescription("The bot counts messages send by users and gives xp for each message, it also detects users in voice who are not muted and gives XP for time spend in voice. The amount of XP gained by each message, time spend in voice and the cooldown between messages can be changed with the {$configCmd} command see {$helpCmd} for more info.\n Role rewards for users are synced whenever they send a message to the server. When removing or adding XP from users their roles will persist until they send a message.");
        $this->addCommands([
            ['cmd' => 'leaderboard', 'usage' => '', 'desc' => 'Show the leaderboard'],
            ['cmd' => 'rank', 'usage' => '`<user_mention>`', 'desc' => 'Show level, xp and messages for you or another user'],
            ['cmd' => 'givexp', 'usage' => '`<user_mention>` `<xp_amount>`', 'desc' => 'Give user xp'],
            ['cmd' => 'removexp', 'usage' => '`<user_mention>` `<xp_amount>`', 'desc' => 'Remove user xp'],
            ['cmd' => 'resetxp', 'usage' => '`<user_mention>`', 'desc' => 'Reset XP for user'],
            ['cmd' => 'rewards', 'usage' => '', 'desc' => 'Show the role rewards for different levels'],
            ['cmd' => 'addreward', 'usage' => '`<level>` `<role_id>`', 'desc' => 'Add a role reward to a level'],
            ['cmd' => 'delreward', 'usage' => '`<level>`', 'desc' => 'Delete role rewards from this level'],
        ], $embedBuilder);
        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function settingsPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("All setting values are numeric, for booleans 0 = false, 1 = true. (`duration_in_voice` / `xp_voice_cooldown`) * `xp_voice_count` = Amount of XP gained, calculated on voice disconnect.");
        $this->addCommands([
            ['cmd' => 'config', 'usage' => '', 'desc' => 'Show all config settings'],
            ['cmd' => 'set', 'usage' => '`<setting_key>` `<setting_value>`', 'desc' => 'Update a setting'],
        ], $embedBuilder);
        $embedBuilder->getEmbed()->addField(
            ['name' => 'xp_count', 'value' => ' Amount of xp you gain per message', 'inline' => true],
            ['name' => 'xp_cooldown', 'value' => 'Cooldown between messages before gain `xp_count` again.', 'inline' => true],
            ['name' => 'xp__voice_count', 'value' => ' Amount of xp you gain in voice', 'inline' => true],
            ['name' => 'xp_voice_cooldown', 'value' => 'Over how much time the XP is given.', 'inline' => true],
            ['name' => 'enable_xp', 'value' => 'Enable the message XP system', 'inline' => true],
            ['name' => 'enable_voice_xp', 'value' => 'Enable the voice XP system', 'inline' => true],
            ['name' => 'enable_emote_counter', 'value' => 'Enable emote counter', 'inline' => true],
            ['name' => 'enable_role_rewards', 'value' => 'Enable role rewards', 'inline' => true],
            ['name' => 'enable_bump_counter', 'value' => 'Enable bump counter', 'inline' => true],
            ['name' => 'enable_reactions', 'value' => 'Enable custom reactions', 'inline' => true],
            ['name' => 'enable_commands', 'value' => 'Enable custom commands', 'inline' => true],
            ['name' => 'enable_logging', 'value' => 'Enable general logs', 'inline' => true],
            ['name' => 'log_channel', 'value' => 'ID of the log channel', 'inline' => true],
            ['name' => 'enable_bump_reminder', 'value' => 'Enable the bump reminder', 'inline' => true],
            ['name' => 'bump_reminder_role', 'value' => 'What role should be tagged for the bump reminder', 'inline' => true],
            ['name' => 'bump_channel', 'value' => 'Channel where to tag the role', 'inline' => true],
            ['name' => 'enable_mention_responder', 'value' => ' Enable the mention responder', 'inline' => true],
            ['name' => 'enable_qotd_reminder', 'value' => 'Enable the role mention in set question of the day channel', 'inline' => true],
            ['name' => 'qotd_channel', 'value' => 'Channel to tag qotd role', 'inline' => true],
            ['name' => 'qotd_role', 'value' => 'role to tag in qotd channel', 'inline' => true],
            ['name' => 'current_count', 'value' => 'Current counter', 'inline' => true],
            ['name' => 'enable_count', 'value' => 'Enable counting channel', 'inline' => true],
            ['name' => 'count_channel', 'value' => 'Counting channel', 'inline' => true],
            ['name' => 'enable_join_role', 'value' => 'Enable giving a role when a user joins the server', 'inline' => true],
            ['name' => 'join_role', 'value' => 'Role to give to users when they join the server', 'inline' => true],
        );
        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function logsPage(EmbedBuilder $embedBuilder): EmbedBuilder
    {
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
        $this->addCommands([
            ['cmd' => 'logconfig', 'usage' => '', 'desc' => 'See the log config'],
            ['cmd' => 'logset', 'usage' => '`<key>` `<value>`', 'desc' => 'Update a value in the log config'],
        ], $embedBuilder);
        return $embedBuilder;
    }

    /**
     * @param EmbedBuilder $embedBuilder
     * @return EmbedBuilder
     */
    public function mentionResponder(EmbedBuilder $embedBuilder): EmbedBuilder
    {
        $embedBuilder->setDescription("Small funny feature, when you tag the bot you will get a random reply from a list of mention replies. There are default replies, but you can also add your own replies based on certain roles in the server.");
        $this->addCommands([
            ['cmd' => 'replies', 'usage' => '`<group_id>`', 'desc' => 'Show all replies or filter by group'],
            ['cmd' => 'groups', 'usage' => '', 'desc' => 'Show all groups'],
            ['cmd' => 'addgroup', 'usage' => '`<discord_role>` `<user/role>` `<multiplier>`', 'desc' => 'Add a group'],
            ['cmd' => 'updategroup', 'usage' => '`<id>` `<user/role>` `<multiplier>`', 'desc' => 'Update a group'],
            ['cmd' => 'delgroup', 'usage' => '`<group_id>`', 'desc' => 'Delete a group'],
            ['cmd' => 'addreply', 'usage' => '`<group_id>` `<reply_line>`', 'desc' => 'Add a reply to a group'],
            ['cmd' => 'delreply', 'usage' => '`<reply_id>`', 'desc' => 'Remove a reply'],
        ], $embedBuilder);
        return $embedBuilder;
    }
}
