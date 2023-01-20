# Introduction

Discord bot written in PHP, using laravel and the DiscordPHP package. Initially created just for our own server but
now runs on as many serves as you like. If you have questions DM my directly by my discord tag: `berend#0579`.

Special thanks to:
- Angel for writing most of the Skyrim lines used in the MentionResponder.
- Richard for bug testing.
- Ricardo for working out ideas to implement.

## Installing
1. Download this repo and install al its dependencies with `composer install`
2. Create your env file `mv .env.example .env` and fill in database credentials and bot token
3. You can edit the `GuildSeeder` and `DiscordUsersSeeder` to add your server and admin users
4. Run `php artisan migrate --seed` to setup your database.
## Running the bot
1. I added an artisan command to run the bot `php artisan bot:run`.
2. In order to make the bump reminder work you also need to run the queue `php artisan queue:work`
3. Make sure both these run in the background while you exit your connection / terminal. Or keep two terminals running
   if you want to test it locally.

# Functions
I will try to update this readme with new functionality as I add it, but I cannot promise I keep it entirely up to date.
The bot only uses slash commands, there used to be message commands but I moved everything to slash only. I tried
to filter incoming user input as much as possible with the slash commands itself. For example allowing only roles
when roles are required, integers when levels are required etc. Even with changing bot settings it reads the settings 
from the database and will be preloaded in the slash command so you do not have to remember each one.

## Multiple servers
The bot runs on multiple servers, but each server requires some settings and values to be set in the database,
the plan is to let the bot do it automatically whenever he is invited and joins a server. For now it needs to be
done manually or the best way is to add your guild/server to the seeder and everything will be set properly.

## Roles and permissions

The bot uses a permissions and role system. Permissions are coded into the bot and available for you to assign
to as many roles as you like, those roles can be assigned to as many users as you like. Users can also have multiple
roles.

### Permissions

* `roles`
* `create-role`
* `delete-role`
* `update-role`
* `permissions`
* `attach-permission`
* `attach-role`
* `config`
* `timeouts`
* `channels`
* `add-cringe`
* `delete-cringe`
* `commands`
* `reactions`
* `role-rewards`
* `manage-xp`
* `logs`

### Default Roles

* **Admin**
    * All permissions
* **Moderator**
    * `timeouts`
    * `channels`
    * `commands`
    * `reactions`

### Commands

* **roles** • Overview of all roles and their permissions
* **permissions** • Overview of all permissions
* **users** • Overview of all users and their roles
* **myroles** • See your roles
* **userroles** `user_mention` • See roles from user
* **addrole** `role_name` • Add a new role
* **delrole** `role_name` • Delete a role
* **addperm** `role_name` `perm_name1,perm_name2` • Add permission(s) to a role
* **delperm** `role_name` `perm_name1,perm_name2` • Remove permission(s) from a role
* **adduser** `user_mention` `role_name` • Add user to the given role
* **deluser** `user_mention` `role_name` • Remove user from given role";

## Levels and XP

If enabled users can gain xp by sending messages and hanging out in voice. Check the Bot config section below
for more control how much xp people gain per message, per minute in voice and if you want to enable this functionality
at all. It is possible to attach role rewards to levels, when a user reaches that specific level a new role is granted.

For gaining xp in voice we have two extra rules:
- Muted users (either mic muted or both) do not gain XP.
- Users in a voice channel marked as `no_xp` will not gain XP.

More on how to set the no_xp flag to a channel later.

### Calculating Levels

This is what I use to calculate required XP for each level: `5 * (lvl ^ 2) + (50 * lvl) + 100 - xp` it is the exact same 
XP system as MEE6 uses. You can read more here ->  https://github.com/Mee6/Mee6-documentation/blob/master/docs/levels_xp.md


### Commands

* **leaderboard** • Show the leaderboard with the highest ranking members at the top
* **rank** `user_mention` • Show your own level, xp and messages or that of another user
* **givexp** `user_mention` `xp_amount` • Give user xp
* **removexp** `user_mention` `xp_amount` • Remove user xp
* **resetxp** `user_mention` • Reset XP for user
* **rewards** • Show the role rewards for different levels
* **addreward** `level` `role_id` • Add a role reward to a level
* **delreward** `level` • Delete role rewards from this level";

## Bot Config

The bot loads a config from the settings table which can be viewed and changed, it allows only integer values! So when 
setting channels or roles make sure to use the IDS.

* **config**
* **set** `setting_name` `new_value`

Right now we have the following settings:

* `xp_count` - xp gained per message
* `xp_cooldown` - seconds cooldown after last message before it counts again
* `xp_voice_count` - xp gained in voice
* `xp_voice_cooldown` - cooldown for xp gain in voice
* `enable_xp`  - enable the message xp system
* `enable_voice_xp` - enable the voice xp system
* `enable_emote_counter` - enable emote counters
* `enable_role_rewards`  - enable role rewards for
* `enable_bump_counter` - enable bump counter
* `enable_reactions` - enable custom reactions
* `enable_commands` - enable custom commands
* `enable_logging` - enable general logging
* `log_channel_id` - set the channel ID where the log sends messages
* `enable_bump_reminder` - enable 2 hour tag for people who want to bump the discord
* `bump_reminder_role` - Role to be tagged for bump reminders
* `bump_channel` - Channel where the bump reminders are tagged

## Logging

The bot is able to log some events to a specific log channel, you have to set the following two settings properly
in the bot config to make it work:

- `enable_logging` - Enable the general log
- `log_channel_id` - Id of the channel where the log sends messages

### Events
I pretty much copied the way MEE6 logs events, it looks the same but it logs more and there is no delay. Not sure
why MEE6 is sometimes so laggy.

The following events are logged:
- Joined server
- Left server
- Kicked from server
- Banned from server
- Unbanned from server
- Received timeout
- Joined voice call
- Left voice call
- Switched voice call
- Muted in voice by moderator
- Unmuted in voice by moderator
- Updated username (old and new username)
- Message updated (show old and new message)
- Message deleted (show deleted message)
- Invite created
- Invite removed
- Started streaming in voice
- Stopped streaming in voice
- Enabled his webcam in voice
- Disabled his webcam in voice

You can enable/disable each event in the log config:

* **logconfig** - Look at the log config
* **logset** `key` `value` - Enable or disable one of the events

## Channels

You can set flags for channels, the no_xp flag can also be used for voice channels! For now there are two flags you can use:

* `no_xp` - Bot does not count xp for users when they send messages in this channel
* `media_only` - Channel allows only media and URL, anything else will be deleted.

### Commands

* **channels**
* **markchannel** `channel` `flag`
* **unmarkchannel** `channel` `flag`

## Reactions

Certain strings can be added to the bot and when those strings are detected
in a message the bot will add a reaction to the message with a set emote. These
reactions can be added, removed and viewed with commands so nothing needs to be
done in code.

Commands to manage reactions are:

* **reactions**
* **addreaction** `<word_trigger` `<reaction_emote>`
* **delreaction** `<word_trigger`

## Simple commands

Simple commands such as $ping -> response pong can be added in discord as well.
Same as with the reactions you can add and remove and view as many as you like
without the necessity to enter any code. By default these command trigger do not include the bot
prefix ($), so if you want to trigger on prefix you need to include the bot prefix in the command.

Commands to manage simple commands are:

* **commands**
* **addcmd** `<command>` `<response>`
* **delcmd** `<command>`

## Emote Counter

The bot counts both custom and default emotes! These can be retrieved by using:

* **emotes**

## Timeout detection

We are not satisfied with the audit log and how timeouts are displayed and
filtered. It is not easy to quickly look up how often somebody has been timed
out, for what reason and how long. Every timeout is automatically saved
in the database including the reason and duration. We can easily see a history
of timeouts + filter timeouts only for specific users.

Commands to show timeouts are:

* **timeouts**
* **usertimeouts** `<user_mention>`

## Moderator statistics

The bot counts timeouts bans and kicks for moderators with more than 500 access to the bot! More will be added
in the future.

* **modstats**

## Bump Counter & Reminder

We use a bot to add our discord server to an external website, once every
2 hours you can use this bot to get back on the front page. To encourage
people in our server to use the bump our bot counts for every member how often
they bump. At the end of the month we will check the highest member and he
or she will get some nice perks!

Command to view the bump statistics is

* **bumpcounter** `time-range` • You can see bumps of all time, or only for this month. Monthly bumps are the statistics for who gets the bumper elite role!

In order to make the bump reminder work, you need to set the following 3 settings:
* `enable_bump_reminder` - Enable 2 hour tag for people who want to bump the discord
* `bump_reminder_role` - Role to be tagged for bump reminders
* `bump_channel` - Channel where the bump reminders are tagged

Use the `set` command for it, setting names will be preloaded for you to pick from using the slash commands.

## Cringe Counter

Sometimes people on discord can be quite cringe, some more than others.
We can add cringe counters to member to see who makes the most cringe
comments. Just a funny little feature! You can only add and delete a single cringe
at the time. No deleting everything at once!

commands to use cringe is:

* **addcringe** `<user>`
* **delcringe** `<user>`
* **cringecounter**

## Mention Responder
Small funny feature, when you tag the bot you will get a random reply from a list of mention replies. There are default
replies, but you can also add your own replies based on certain roles in the server, for example (our server):
- Weeb role
- NSFW role
- Moderator role
- Admin role
- Strijder Role

The bot comes with some default groups and replies:
- If you have a high rank in the server (according to the xp system)
- If you are the highest person on the leaderboard (according to the xp system)
- If you bumped the discord the most (all time)
- If you bumped the discord a lot
- If you had timeouts in the past
- If you are highly ranked on the cringe counter leaderboard

You can manage all groups and their replies by using these commands:
- `replies` - show all replies grouped by available categories
- `replies` `group_id` - Show all replies for a single group
- `groups` - Show all groups
- `addgroup` `discord_role` - Add a group 
- `delgroup` `group_id` - Delete a group and its replies (!!!)
- `addreply` `group_id` `reply_line` - Add a reply to a group
- `delreply` `reply_id`- Delete a reply 


## Fun commands

A few fun commands you can use

* **urb** `search_term` - Searches on urban dictionary
* **8ball** `question` - Ask a question to the 8ball
* **ask** `question` - Ask a question and get a gif response
* **say** `something` - say something

## Help command

You can write `/help` on discord to get information on most commands in the bot and explanation how they work. There are
different categories which are preloaded for you to pick from.

That's it for now! Enjoy! :)
