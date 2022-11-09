<?php

namespace App\Discord;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\Permission;
use Discord\Builders\MessageBuilder;

class Help extends SlashAndMessageCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'help';
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.help.title'))
            ->setFooter(__('bot.help.footer'));

        if (isset($this->message)) {
            $parameters = explode(' ', $this->message->content);

            if (isset($parameters[1])) {
                if (strtolower($parameters[1]) === 'roles') {
                    $desc = "`roles` • Overview of all roles and their permissions
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
                } elseif (strtolower($parameters[1]) === 'moderation') {
                    $desc = "Timeouts are automatically detected and saved, bans and kicks are only counted.\n
                    `timeouts` • Show given timeout history
                    `usertimeouts` `user_mention` • Show timeout history for user
                    `modstats` • Show moderator statistics
                    `config` • See server configuration
                    `setconfig` `setting_key` `new_value` • Update server setting
                     `mediachannels` • Shows a list of channels configured for media only
                    `addmediachannel` `channel` • Mark a channel as media only
                    `delmediachannel` `channel` • Delete channel from media only list
                    `commands` • Show list of custom commands
                    `addcmd` `command` `response` • Add a custom command
                    `delcmd` `command` • Remove a custom command";
                    $embedBuilder->setDescription($desc)->setTitle("Moderation");
                } elseif (strtolower($parameters[1]) === 'fun') {
                    $desc = "`cringecounter` • Show who is most cringe..
                    `addcringe` `user_mention` • Increase cringe counter
                    `delcringe` `user_mention` • Decrease cringe counter
                    `resetcringe` `user_mention` • Reset cringe counter\n
                    `reactions` • Show list custom reactions
                    `addreaction` `trigger` `emoji` • Add new reactions
                    `delreaction` `trigger` • Delete a reaction\n
                    `bumpstats` • Show bumper elites leaderboard
                    `emotes` • Shows scoreboard of most used emotes
                    `messages` • Shows messages and XP gained over time
                    `xp` • Shows your current message counter and XP
                    `urb` `search_term` • Search something on urban dictionary
                    `8ball` `question` • Ask the magic 8ball
                    `ask` `question` • Yes? No? Hmm..?";
                    $embedBuilder->setDescription($desc)->setTitle("Fun commands");
                }
                return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
            }
        }

        $embedBuilder->getEmbed()->addField(
            ['name' => 'Help Files', 'value' => "All commands use `$` prefix, alternatively you can use slash commands `/`.\n Use `\$help <section_title>` for more extensive explanation.\n For example `\$help roles` "],
            ['name' => 'Roles', 'value' => 'Managing roles and permissions'],
            ['name' => 'Moderation', 'value' => 'Moderator actions'],
            ['name' => 'Fun', 'value' => 'Fun commands'],
        );
        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
