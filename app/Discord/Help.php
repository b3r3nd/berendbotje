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
                $desc = "The main Admin role cannot be deleted, permissions cannot be removed from the admin role and the role cannot be removed from the owner of the guild.

                    `roles` • Overview of all roles and their permissions
                    `permissions` • Overview of all permissions
                    `users` • Overview of all users and their roles
                    `myroles` • See your roles
                    `userroles` `user_mention` • See roles from user
                    `addrole` `role_name` • Add a new role
                    `delrole` `role_name` • Delete a role
                    `addperm` `role_name` `perm_name1,perm_name2` • Add permission(s) to a role
                    `delperm` `role_name` `perm_name1,perm_name2` • Remove permission(s) from a role
                    `adduser` `user_mention` `role_name` • Add user to the given role
                    `deluser` `user_mention` `role_name` • Remove user from given role";
                $embedBuilder->setDescription($desc)->setTitle("Roles and Permissions");
            } elseif (strtolower($this->arguments[0]) === 'moderation') {
                $desc = "Timeouts are automatically detected and saved, bans and kicks are only counted.\n
                    `timeouts` • Show given timeout history
                    `usertimeouts` `user_mention` • Show timeout history for user
                    `modstats` • Show moderator statistics
                    `config` • See server configuration
                    `set` `setting_key` `new_value` • Update server setting
                     `mediachannels` • Shows a list of channels configured for media only
                    `addmediachannel` `channel` • Mark a channel as media only
                    `delmediachannel` `channel` • Delete channel from media only list
                    `commands` • Show list of custom commands
                    `addcmd` `command` `response` • Add a custom command
                    `delcmd` `command` • Remove a custom command";
                $embedBuilder->setDescription($desc)->setTitle("Moderation");
            } elseif (strtolower($this->arguments[0]) === 'levels') {
                $desc = "The bot counts messages send by users and gives xp for each message, it also detects users in voice who are not muted and gives XP for time spend in voice. The amount of XP gained by each message, time spend in voice and the cooldown between messages can be changed with the `config` command see `help settings` for more info.\n Role rewards for users are synced whenever they send a message to the server. When removing or adding XP from users their roles will persist until they send a message.\n
                    `leaderboard` • Show the leaderboard with highest ranking members at the top
                    `rank` • Show your own level, xp and messages
                    `givexp` `user_mention` `xp_amount` • Give user xp
                    `removexp` `user_mention` `xp_amount` • Remove user xp
                    `resetxp` `user_mention` • Reset XP for user
                    `rewards` • Show the role rewards for different levels
                    `addreward` `level` `role_id` • Add a role reward to a level
                    `delreward` `level` • Delete role rewards from this level";
                $embedBuilder->setDescription($desc)->setTitle("Levels and XP");
            } elseif (strtolower($this->arguments[0]) === 'fun') {
                $desc = "`cringecounter` • Show who is most cringe..
                    `addcringe` `user_mention` • Increase cringe counter
                    `delcringe` `user_mention` • Decrease cringe counter
                    `resetcringe` `user_mention` • Reset cringe counter\n
                    `reactions` • Show list custom reactions
                    `addreaction` `trigger` `emoji` • Add new reactions
                    `delreaction` `trigger` • Delete a reaction\n
                    `bumpstats` • Show bumper elites leaderboard
                    `emotes` • Shows scoreboard of most used emotes
                    `leaderboard` • Shows leaderboard based on messages and xp gained
                    `rank` • Shows your current rank, message counter and XP
                    `urb` `search_term` • Search something on urban dictionary
                    `8ball` `question` • Ask the magic 8ball
                    `ask` `question` • Yes? No? Hmm..?
                    ";
                $embedBuilder->setDescription($desc)->setTitle("Fun commands");
            } elseif (strtolower($this->arguments[0]) === 'settings') {
                $desc = "All setting values are numeric, for booleans 0 = false, 1 = true.

                    **Commands**
                    `config` • Show all settings
                    `set` `setting_key` `setting_value` • Update a setting

                     **Settings**
                    `xp_count` • Amount of xp you gain per message .
                    `xp_cooldown` • Cooldown between messages before gain `xp_count` again.

                    (`duration_in_voice` / `xp_voice_cooldown`) * `xp_voice_count` = Amount of XP gained, calculated on voice disconnect.
                    `xp_voice_count` • Amount of xp you gain in voice
                    `xp_voice_cooldown` • Over how much time the XP is given.

                    `enable_xp` • Enable the message XP system
                    `enable_voice_xp` • Enable the voice XP system
                    `enable_emote_counter` • Enable emote counter
                    `enable_role_rewards` • Enable role rewards
                    `enable_bump_counter` • Enable bump counter
                    `enable_reactions` • Enable custom reactions
                    `enable_commands` • Enable custom commands
                    `enable_logging` • Enable general logs
                    `log_channel` • ID of the log channel

                    **Examples:**
                    `set` `enable_xp` `0` • Disable message XP system
                    `set` `enable_xp` `1` • Enable message XP system
                    `set` `xp_count` `50` • Set the XP gain for each message to 50
                    ";
                $embedBuilder->setDescription($desc)->setTitle("Settings");
            } elseif (strtolower($this->arguments[0]) === 'logs') {
                $desc = "You must enable logging by changing the following settings:
                `set` `enable_logging` `1` • Enable the general logging
                `set` `log_channel_id` `channel_id` • Set the log channel **use channel ID**

                 **Logged Events**
                 - Joined server
                 - Left server
                 - Kicked from server
                 - Banned from server
                 - Received timeout
                 - Joined voice call
                 - Left voice call
                 - Updated username (old and new username)
                 - Message updated (show old and new message)
                 - Message deleted (show deleted message)";
                $embedBuilder->setDescription($desc)->setTitle("Logs");
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
            ['name' => 'Fun', 'value' => 'Fun commands'],
        );
        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}

