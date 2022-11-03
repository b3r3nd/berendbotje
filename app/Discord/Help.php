<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use Discord\Builders\MessageBuilder;

class Help extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
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
                if (strtolower($parameters[1]) === 'admins') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Admins', 'value' => 'For now there are 3 defined access levels in use, `900`, `500` and `100`. `900` is only for administrators to manage the bot, `500` is for moderators who can also add and remove reactions for example. `100` is for users, these can increase cringe counters, add music to the queue and other basic stuff.'],
                        ['name' => 'Create Admin', 'value' => 'addadmin `user_mention` `access_level`'],
                        ['name' => 'Delete Admin', 'value' => 'deladmin `user_mention`'],
                        ['name' => 'Update Admin Level', 'value' => 'clvladmin `user_mention` `access_level`'],
                        ['name' => 'Check your access', 'value' => 'access'],
                        ['name' => 'Admin Overview', 'value' => 'admins'],
                        ['name' => 'Moderator statistics', 'value' => 'modstats'],
                        ['name' => 'Settings overview', 'value' => 'config'],
                        ['name' => 'Update setting', 'value' => 'set `setting_key` `setting_value`'],
                        ['name' => 'Add server', 'value' => 'addserver `server_id` `owner_account_id`'],
                    );
                } elseif (strtolower($parameters[1]) === 'music') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Music Player', 'value' => 'Youtube music player with queue.'],
                        ['name' => 'Add song to queue', 'value' => 'addsong `youtube_url`'],
                        ['name' => 'Remove song from queue', 'value' => 'delsong `queue_position`'],
                        ['name' => 'Start player', 'value' => 'play'],
                        ['name' => 'Stop player', 'value' => 'stop'],
                        ['name' => 'Show queue', 'value' => 'queue'],
                    );
                } elseif (strtolower($parameters[1]) === 'cringe') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Cringe Counter', 'value' => 'Keep track of who is most cringe in our discord.'],
                        ['name' => 'Increase cringe counter', 'value' => 'addcringe `user_mention`'],
                        ['name' => 'Decrease cringe counter', 'value' => 'delcringe `user_mention`'],
                        ['name' => 'Reset cringe counter', 'value' => 'resetcringe `user_mention`'],
                        ['name' => 'Show cringe counter', 'value' => 'cringecounter'],
                    );
                } elseif (strtolower($parameters[1]) === 'timeouts') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Timeouts', 'value' => 'Timeouts are automatically detected and added to this list, including the giver and reason.'],
                        ['name' => 'All timeouts', 'value' => 'timeouts'],
                        ['name' => 'Single user timeouts', 'value' => 'usertimeouts `user_mention`'],
                    );
                } elseif (strtolower($parameters[1]) === 'bumper') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Bumper Elite', 'value' => 'Statistics on how often people bump the server. Your counter is increased by using /bump in #bump'],
                        ['name' => 'Bump statistics', 'value' => 'bumpstats'],
                    );
                } elseif (strtolower($parameters[1]) === 'reactions') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Simple Reactions', 'value' => 'Manage simple emote reactions to text strings.'],
                        ['name' => 'Create reaction', 'value' => 'addreaction `text_trigger` `emote_response`'],
                        ['name' => 'Delete reaction', 'value' => 'delreaction `text_trigger`'],
                        ['name' => 'Show reactions', 'value' => 'reactions'],
                    );
                } elseif (strtolower($parameters[1]) === 'commands') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Simple Commands', 'value' => 'Manage simple commands which require only text responses'],
                        ['name' => 'Create command', 'value' => 'addcmd `text_trigger` `text_response`'],
                        ['name' => 'Delete command', 'value' => 'delcmd `text_trigger`'],
                        ['name' => 'Show commands', 'value' => 'commands'],
                    );
                } elseif (strtolower($parameters[1]) === 'media') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Media channels', 'value' => 'Configure channels as media only channels, in these channels only files and links can be send.'],
                        ['name' => 'Create media channel', 'value' => 'addmediachannel `channel`'],
                        ['name' => 'Delete media channel', 'value' => 'delmediachannel `channel`'],
                        ['name' => 'Show media channels', 'value' => 'mediachannels'],
                    );
                } elseif ($parameters[1] === 'fun') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Fun Commands', 'value' => 'Small fun commands :)'],
                        ['name' => 'Urban Dictionary', 'value' => 'urb `search_term`'],
                        ['name' => '8ball', 'value' => '8ball `your_question`'],
                        ['name' => 'ask', 'value' => 'Yes or no?'],
                        ['name' => 'say', 'value' => 'say `whatever_you_want`'],
                        ['name' => 'Emote Counter', 'value' => 'Global emote counter, to see a list use `emotes`'],
                    );
                } elseif ($parameters[1] === 'messages') {
                    $embedBuilder->getEmbed()->addField(
                        ['name' => 'Messages Counter', 'value' => 'Counts your messages and gives you xp. Over time you gain levels and server roles.'],
                        ['name' => 'messages', 'value' => 'Overview of all users, their message count and xp'],
                        ['name' => 'xp', 'value' => 'Show your own messages and xp'],
                    );
                }
                return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
            }
        }

        $embedBuilder->getEmbed()->addField(
            ['name' => 'Help Files', 'value' => "All commands use `$` prefix, alternatively you can use slash commands `/`.\n Use `\$help <section_title>` for more extensive explanation.\n For example `\$help admins` "],
            ['name' => 'Admins', 'value' => 'Managing admins, moderators and config files.'],
            ['name' => 'Music', 'value' => 'Youtube music player with queue.'],
            ['name' => 'Cringe', 'value' => 'Keep track of who is most cringe in our discord.'],
            ['name' => 'Timeouts', 'value' => 'Timeout detection and overview.'],
            ['name' => 'Bumper', 'value' => 'Statistics on how often people bump the server.'],
            ['name' => 'Reactions', 'value' => 'Manage simple emote reactions to text strings.'],
            ['name' => 'Commands', 'value' => 'Manage simple commands which require only text responses'],
            ['name' => 'Media', 'value' => 'Configure channels as media only channels'],
            ['name' => 'Messages', 'value' => 'Counts your messages and gives you xp. Over time you gain levels and server roles.'],
            ['name' => 'Fun', 'value' => 'Small fun commands :)'],
        );

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
