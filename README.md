# Introduction
Discord bot written in PHP using laravel and the DiscordPHP package. Initially created just for our own server but
is now able to run on multiple servers. This project is still a work in progress and not everything might be 
completely user-friendly. Even though it is a general purpose discord bot, I try to add on top what discord already
offers instead of replacing functionality entirely.

Check the `/help` command in discord or if you have questions DM me directly by my discord tag: `drerrievandebuurt`.

Special thanks to:
- Angel for writing most of the Skyrim lines used in the MentionResponder.
- Richard for bug testing.
- Ricardo for working out ideas to implement.
- Justin & Stefan for ideas mental support.

# Installing
1. Download this repo and install al its dependencies with `composer install`
2. Create your env file `mv .env.example .env` and fill in database credentials and bot/api tokens
3. You can edit the `GuildSeeder` and `DiscordUsersSeeder` to add your discord account and server.
```php
 class DiscordUsersSeeder extends Seeder
{
    public function run()
    {
        $ids = [
            '<discord_owner_id_here>', 
        ];
        foreach ($ids as $id) {
            DiscordUser::factory()->create([
                'discord_id' => $id,
            ]);
        }
    }
}
```

```php
class GuildSeeder extends Seeder
{
    public function run()
    {
        $guilds = [
            [
                'name' => '<guild_name_here>',
                'guild_id' => '<guild_id_here>', // Servers are guilds
                'owner_id' => 1, // First user added in DiscordUsersSeeder
            ],
        ];
        foreach ($guilds as $guild) {
            Guild::factory()->create($guild);
        }
    }
}
```
4. When that is done run `php artisan migrate --seed` to setup your database.
5. Check the env file for any settings you want to change. Commands can be disabled for specific guilds by using its config in discord. However, you can disable parts of the bot entirely here, the commands will not be loaded.

```
ENABLE_FUN=TRUE
ENABLE_LEVELS=TRUE
ENABLE_LOGGER=TRUE
ENABLE_MENTION_RESPONDER=TRUE
ENABLE_ROLES=TRUE
ENABLE_SETTINGS=TRUE
ENABLE_MOD=TRUE
ENABLE_HELP=TRUE
```
6. Also make sure to fill in any tokens and hosts if you wish to use the corresponding commands:
```
BOT_TOKEN=
URB_TOKEN=
URB_HOST=
OPENAI_API_KEY=
OPEN_AI_HOST=
```
7. You can use the following commands to run the bot:
- `bot:run` - Run the bot normally - does not create, update or delete slash commands
- `bot:run --updatecmd` - Creates/Updates all slash commands first
- `bot:run --delcmd` - Deletes all slash commands first
- `bot:run --dev` - Load only test commands but always update the slash command
8. In order to make the image generation with open ai and reminders work you also need to run the queue `php artisan queue:work` and install redis!

# Functions
I will try to update this readme with new functionality as I add it, however I cannot promise I keep it entirely up to date.
The bot only uses slash commands, there used to be message commands, however I moved everything to slash only. I tried
to filter incoming user input as much as possible with the slash commands itself. For example allowing only roles
when roles are required, integers when levels are required etc. Even with changing bot settings it reads the settings 
from the database and will be preloaded in the slash command, so you do not have to remember each one.

## Multiple servers
The bot runs on multiple servers, but each server requires some settings and values to be set in the database,
the plan is to let the bot do it automatically whenever he is invited and joins a server. For now, it needs to be
done manually. The best way is to add your guild/server to the seeder and everything will be set properly.

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
* `role_rewards`
* `manage_xp`
* `add_mention`
* `del_mention`
* `manage_mention_group`
* `openai`
* `abusers`

### Default Roles

* **Admin**
* **Moderator**

### Commands

* **roles** • Overview of all roles and their permissions
* **permissions** • Overview of all permissions
* **users** • Overview of all users and their roles
* **role** `user_mention` • See your roles or those from another user
* **addrole** `role_name` • Add a new role
* **delrole** `role_name` • Delete a role
* **addperm** `role_name` `perm_name` • Add permission(s) to a role
* **delperm** `role_name` `perm_name` • Remove permission(s) from a role
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
* `enable_role_rewards`  - enable role rewards for gaining levels
* `enable_bump_counter` - enable bump counter
* `enable_reactions` - enable custom reactions
* `enable_commands` - enable custom commands
* `enable_logging` - enable general logging
* `log_channel_id` - set the channel ID where the log sends messages
* `enable_bump_reminder` - enable reminder tag
* `bump_reminder_role` - Role to be tagged for bump reminders
* `bump_channel` - Channel where the bump reminders are tagged
* `enable_mention_responder` - Enable the responses when you mention the bot
* `enable_qotd_reminder` - Enable the role mention in set question of the day channel
* `qotd_channel` - Channel to tag qotd role
* `qotd_role` - Role to tag in qotd channel   
* `current_count` - Current counter for counting channel
* `enable_count` - Enable the counting channel
* `count_channel` - Counting channel ID

## User config
Users have their own settings per guild. For now, I only use a single setting but more to come soon.

- `no_role_rewards` - Disables gaining role rewards based on levels (if the guild has it enabled)

You can use the following commands to view/change your own settings:
- **userconfig** - Show your config
- **userset** `<setting_key>` `<setting_value>` - Update a value in your user config

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

## Moderation
We have some commands which can help moderate the server, or improve other things!

### Channels

You can set flags for channels, the no_xp flag can also be used for voice channels! For now there are four flags you can use:

* `no_xp` - Bot does not count xp for users when they send messages in this channel
* `media_only` - Channel allows only media and URL, anything else will be deleted.
* `no_stickers` - Stickers will be removed from the chat
* `no_log` - Message logging is disabled for this channel

* **channels** - List of channels with flags set
* **markchannel** `channel` `flag` - add flag to a channel
* **unmarkchannel** `channel` `flag` - remove flag from a channel

### Timeout detection
We are not satisfied with the audit log and how timeouts are displayed and
filtered. It is not easy to quickly look up how often somebody has been timed
out, for what reason and how long. Every timeout is automatically saved
in the database including the reason and duration. We can easily see a history
of timeouts + filter timeouts only for specific users.

* **timeouts** `<user_mention>` - All timeouts or those from a specific user
* **edittimeout** `<timeout_id>` `<new_reason>`- Update the reason for a timeout
* **removetimeout** `<timeout_id>` - Remove a timeout from the log only

### Blacklist
People can be put on a blacklist manually or automatically.

* **blacklist** - Show everyone on the blacklist
* **block** `<user_mention` `<reason>` - Add someone to the blacklist
* **unblock** `<user_mention>`  - Remove someone from the blacklist

## Mention Responder
Small funny feature, when you tag the bot you will get a random reply from a list of mention replies. There are default
replies, but you can also add your own replies based on certain roles in the server.

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
A few fun commands you can use!

* **urb** `search_term` - Searches on urban dictionary
* **8ball** `question` - Ask a question to the 8ball
* **ask** `question` - Ask a question and get a gif response
* **say** `something` - say something
* **modstats** - Who got the power?
* **image** - Generate an image using openai
* **emotes** - List of most used emotes in the guild
* **bumpcounter** `time-range` • Check the monthly or all time bump leaderboard
* **addcringe** `<user>` - Increase someone's cringe counter
* **delcringe** `<user>` - Decrease someone's cringe counter
* **cringecounter** - Reset someone's cringe counter

### Bump Reminder
In order to make the bump reminder work, you need to set the following 3 settings:
* `enable_bump_reminder` - Enable 2 hour tag for people who want to bump the discord
* `bump_reminder_role` - Role to be tagged for bump reminders
* `bump_channel` - Channel where the bump reminders are tagged

### Reactions

Certain strings can be added to the bot and when those strings are detected
in a message the bot will add a reaction to the message with a set emote. These
reactions can be added, removed and viewed with commands so nothing needs to be
done in code.

* **reactions**
* **addreaction** `<word_trigger` `<reaction_emote>`
* **delreaction** `<word_trigger`

### Simple commands

Simple commands such as $ping -> response pong can be added in discord as well.
Same as with the reactions you can add and remove and view as many as you like
without the necessity to enter any code. By default, these command triggers do not include the bot
prefix, so if you want to trigger on prefix you need to include the bot prefix in the command.

* **commands**
* **addcmd** `<command>` `<response>`
* **delcmd** `<command>`

### Counting channel
People requested a channel where they can count. You can't count twice in a row and when you make a mistake the
counter is reset to 0, and you are excluding from counting by being put on the blacklist. The bot deletes
any non-numeric messages from the counting channel. When you are blocked the bot will delete and ignore everything
you type. :)

To make it work you need to set the following settings:
* `enable_count` - Enables the counting channel
* `count_channel` - Counting channel

It saves the current count in `current_count`, which you could also change manually.

### Question of the day
In our server some people post questions every day, when they do the bot tags people who want to be reminded
that a new question is posted. This way I do not have to give mention permissions which can be abused easily.

Check these settings:
* `qotd_channel`
* `qotd_role`
* `enable_qotd_reminder`


# Help command
You can write `/help` on discord to get information on most commands in the bot and explanation how they work. 
You can pick your section from the list and get more info.

That's it for now! Enjoy! :)
