# Introduction

Discord bot written in PHP using laravel and the DiscordPHP package. Initially created just for our own server but
is now able to run on multiple servers. This project is still a work in progress and not everything might be
completely user-friendly. Check the `/help` command in discord for more information.

- Invite bot to your server -> https://discord.com/oauth2/authorize?client_id=651378995245613056&scope=bot
- Any questions or support -> https://discord.gg/rVxJBbVNR7
- Top gg link -> https://top.gg/bot/651378995245613056

### Special thanks to
- Justin & Stefan for ideas and mental support.
- Angel for writing most of the Skyrim lines used in the MentionResponder.
- Richard & Ricardo for bug testing and working out ideas.
## Functionality
Short list of what this bot can do:

- Everything is based on guild, can run on multiple servers with its own data
- Role and permissions system with complete control to assign permissions as you see fit
- Leveling by sending messages and hanging out in voice + commands to manage xp
- Role Rewards based on levels
- Custom level up messages
- Extensive bot config where almost all settings can be changed
- Enable / disable server invites through command to allow mods to use it (has separate permission)
- User config where user can set specific settings for that guild
- Extensive logging with ability to enable/disable each event individually
- Adding flags to channels to for example not gain xp there, or not allow stickers, or only allow media
- Automatic timeout detection to get a better overview of who has been timed out, why and how often
- User blacklist where users can be added and prevented from using certain functions of the bot
- Assign roles to users when they join the server
- Custom welcome messages when people join the server
- Add replies based on roles in the server when you mention the bot
- Reminders - send a reminder every xx time in a certain channel
- Add emotes as custom reactions to strings, when those strings are detected the bot will react with set emote
- Add custom message commands with simple responses
- Counting channel
- Question of the day reminder tag
- Count all emotes send in the server
- Cringe counter where you can increase and decrease as people behave in a cringe way (:D)
- Bump statistics, keeps track of how often people bump the server just for fun
- A bunch of other small fun commands like retrieving info from urban dictionary, generate images with AI and more
- Moderator statistics to see how productive moderators are

# Installing

1. Download this repo and install al its dependencies with `composer install`
2. Create your env file `mv .env.example .env` and fill in database credentials
3. Fill in you Discord bot token credentials and any other api credentials:

```
BOT_TOKEN=
APPLICATION_ID=
SUPPORT_GUILD=
DISCORD_API=

URB_TOKEN=
URB_HOST=
OPENAI_API_KEY=
OPEN_AI_HOST=
```

## Database

The bot will set up everything correctly when it detects a guild for the first time. However you can use the seeder
`GuildSeeder` and `DiscordUsersSeeder` to setup any users and guilds manually.

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

- If you let the bot detect guilds itself you can set up the database
  using `php artisan migrate:fresh --seeder=PermissionSeeder`.
- To run all seeders including users and guilds you can use `php artisan migrate:fresh --seed`.

## Managing Slash commands

Managing slash commands is done through a separate cmd app using php artisan. When updating and deleting a bunch
of commands you will be rate limited. Meaning it takes while before everything is registered. When deleting
commands the console output will tell you the progress, however when adding commands the output it only send when 
everything is done (will change in the future), dont ctrl + c!

- `php artisan bot:slash` - Shows current available slash commands in **discord**! Not your config!
- `php artisan bot:slash --update` - Creates/updates all slash commands.
- `php artisan bot:slash --delete` - Delete all slash commands.

## Running the bot

You can use the following commands to run the bot:

- `php artisan bot:run` - Run the bot, **does not create, update or delete slash commands**!
- `php artisan queue:work` - Run de redis queue for reminders.

## Configuration

There are three main configuration files for the bot (which you should bother with):

- `config/commands.php` - Define all slash commands here and their respective classes
- `config/events.php` - Define all event here and their respective classes
- `config/discord.php` - Global configuration - actual values should be in your .ENV file.

### discord.php

The actual values of these config files should be changed in your local .env. I pasted this config here so
you know what all the values mean.

```PHP
    /*
    |--------------------------------------------------------------------------
    | Bot Token
    |--------------------------------------------------------------------------
    |
    | This is your bot token you can see only once when creating your bot
    | in the discord developers portal. KEEP IT SECRET!
    |
    */

    'token' => env('BOT_TOKEN', ' '),

    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    |
    | The application ID is the ID of your bot, it can be found in the dev
    | portal at the top!
    |
    */

    'app-id' => env('APPLICATION_ID', ' '),

    /*
    |--------------------------------------------------------------------------
    | Discord API
    |--------------------------------------------------------------------------
    |
    | We use this API directly to manage our slash commands, instead of doing
    | it through the bot.
    |
    */

    'api' => env('DISCORD_API', 'https://discord.com/api/v10/'),

    /*
    |--------------------------------------------------------------------------
    | Support Guild
    |--------------------------------------------------------------------------
    |
    | ID of your main guild, register any administrative commands there!
    |
    */

    'support-guild' => env('SUPPORT_GUILD', ' '),

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Service providers can be used to "provide a service", basically load
    | and setup some stuff the bot needs. The Event en Command Service
    | Provider are default, I added one for guilds since I need to
    | load all the guilds on boot.
    |
    | Implement the ServiceProvider interface!
    |
    */
    'providers' => [
        EventServiceProvider::class,
        CommandServiceProvider::class,
        GuildServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Other Settings
    |--------------------------------------------------------------------------
    |
    | My bot uses Urban Dictionary and OpenAI API's for some commands, when
    | you create settings in your bot. Add them to your .env, add them
    | here and use this config to access the values.
    |
    */

    'urb-token' => env('URB_TOKEN', ' '),
    'urb-host' => env('URB_HOST', ' '),
    'open-ai-key' => env('OPENAI_API_KEY', ' '),
    'open-ai-host' => env('OPEN_AI_HOST', ' '),
```

### commands.php
I don't like to autoload my commands. I use the subcommand structure for everything and I want complete freedom
to say which command is in which category without being restricted by a certain directory structure, or go into
each command class to define the command group there, this gives you a nice overview.

When adding commands to this list you need to either extend the `SlashCommand` or `SlashIndexCommand` class. I
added the command sub-command structure as described by discord below. Define the array in the same way.

```PHP
    /*
    |--------------------------------------------------------------------------
    | Slash Command Structure
    |--------------------------------------------------------------------------
    |
    | Here you may specify all your slash commands used by the bot. Remember to
    | add classes which extend the SlashCommand or SlashIndexCommand.
    |
    | 'GLOBAL' AND 'GUILD' ARE NOT PART OF THE STRUCTURE!!!!
    |
    |--------------------------------------------------------------------------
    |   command
    |   |
    |   |__ subcommand
    |   |__ subcommand
    |
    |--------------------------------------------------------------------------
    |   command
    |   |
    |   |__ subcommand-group
    |       |
    |       |__ subcommand
    |   |
    |   |__ subcommand-group
    |       |
    |       |__ subcommand
    |       |__ subcommand
    |
    |--------------------------------------------------------------------------
    |   command
    |   |
    |   |__ subcommand-group
    |       |
    |       |__ subcommand
    |   |
    |   |__ subcommand
    |
    */
 ```

### events.php
Events are all defined in the events.php file, with the comments below should be clear.
```PHP
 /*
    |--------------------------------------------------------------------------
    | Discord Events
    |--------------------------------------------------------------------------
    |
    | Discord Events are for example GUILD_CREATE or INTERACTION_CREATE when
    | adding new events always extend the DiscordEvent class and implement
    | the interface required for that specific Event. For example when you
    | Add an event for MESSAGE_DELETE, you implement the MESSAGE_DELETE
    | interface.
    |
    | Some interfaces might be missing if I did not use those events (yet)!
    |
    */
    'events' => [
        InteractionCreate::class,
        MessageCreate::class,
       // etc..
    ],
    /*
    |--------------------------------------------------------------------------
    | Message Events
    |--------------------------------------------------------------------------
    |
    | Message Events trigger on MESSAGE_CREATE, because we have a lot of them
    | we use a kind of wrapper to prevent a lot of duplicate stuff make
    | sure to implement the MessageCreateAction interface.
    |
    */
    'message' => $messageEvents = [
        MessageXpCounter::class,
        // etc..
    ],

```

# Functions
Now on to what the bot actually does.. 

I will try to update this readme with new functionality as I add it. The bot uses only slash commands!
The slash commands all use sub commands to group them together and autocomplete is on where possible for all inputs!

## Permissions
Permission required when this bot gets added to the server.
- View audit log
- Read messages
- Send messages
- Manage messages
- Embed links
- Read message history
- Add Reactions
- Use voice activity

## Roles and permissions

The bot uses a permissions and role system. Permissions are coded into the bot and available for you to assign
to as many roles as you like, those roles can be assigned to as many users as you like. Users can also have multiple
roles.

### Permissions

`View Roles` `Create role`, `Delete role`, `Update role`, `Permissions`, `Update permission from role`, `Update roles from user`,
`Manage the config`, `Manage timeouts`, `Media filter`, `Increase cringe counter`, `Decrease cringe counter`, 
`Manage custom commands`, `Manage reactions`, `Manage rewards`, `Manage xp for users`, `Manage channel flags`,
`Manage log config`, `Add mention replies`, `Remove mention replies`, `Manage reply groups`,
`Manage blacklist`, `Manage server invites`, `Manage custom messages`

### Default Roles

- **Admin**
`View Roles` `Create role`, `Delete role`, `Update role`, `Permissions`, `Update permission from role`, `Update roles from user`,
`Manage the config`, `Manage timeouts`, `Media filter`, `Increase cringe counter`, `Decrease cringe counter`,
`Manage custom commands`, `Manage reactions`, `Manage rewards`, `Manage xp for users`, `Manage channel flags`,
`Manage log config`, `Add mention replies`, `Remove mention replies`, `Manage reply groups`,
`Manage blacklist`, `Manage server invites`, `Manage custom messages`.


- **Moderator**
`Manage timeouts`, `Media filter`, `Increase cringe counter`, `Decrease cringe counter`,
`Manage custom commands`, `Manage reactions`, `Manage channel flags`,
`Add mention replies`, `Remove mention replies`, `Manage reply groups`,
`Manage blacklist`, `Manage custom messages`.

### Commands

* **roles list** • Overview of all roles and their permissions
* **roles add** `role_name` • Add a new role
* **roles delete** `role_name` • Delete a role
* **roles roles** `user_mention` • See your roles or those from another user
* **permissions list** • Overview of all permissions
* **permissions add** `role_name` `perm_name` • Add permission(s) to a role
* **permissions delete** `role_name` `perm_name` • Remove permission(s) from a role
* **users list** • Overview of all users and their roles
* **users add** `user_mention` `role_name` • Add user to the given role
* **users delete** `user_mention` `role_name` • Remove user from given role";

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
XP system as MEE6 uses. You can read more here
->  https://github.com/Mee6/Mee6-documentation/blob/master/docs/levels_xp.md

### Commands

* **xp leaderboard** • Show the leaderboard with the highest ranking members at the top
* **xp rank** `user_mention` • Show your own level, xp and messages or that of another user
* **xp give** `user_mention` `xp_amount` • Give user xp
* **xp remove** `user_mention` `xp_amount` • Remove user xp
* **xp reset** `user_mention` • Reset XP for user
* **rolerewards list** • Show the role rewards for different levels
* **rolerewards add** `level` `role_id` • Add a role reward to a level
* **rolerewards delete** `level` • Delete role rewards from this level";

## Bot Config

The bot loads a config from the settings table which can be viewed and changed, it allows only integer values! So when
setting channels or roles make sure to use the IDS.

* **config guid list**
* **config guild edit** `setting_name` `new_value`

### Settings

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
* `enable_mention_responder` - Enable the responses when you mention the bot
* `enable_qotd_reminder` - Enable the role mention in set question of the day channel
* `enable_join_role` - Enables giving users a role when they join the server
* `enable_welcome_msg` - Enable the welcome message
* `enable_lvl_msg` - Enable level up message
* `enable_bump_reminder` - enable reminder tag
* `enable_count` - Enable the counting channel
* `bump_channel` - Channel where the bump reminders are tagged
* `qotd_channel` - Channel to tag qotd role
* `count_channel` - Counting channel ID
* `welcome_msg_channel` - Channel to welcome user
* `level_up_channel` - Channel to send level up messages
* `log_channel_id` - set the channel ID where the log sends messages
* `bump_reminder_role` - Role to be tagged for bump reminders
* `qotd_role` - Role to tag in qotd channel
* `join_role` - Actual role to give when a user joins the server
* `current_count` - Current counter for counting channel

## User config

Users have their own settings per guild. For now, I only use a single setting but more to come soon.

- `no_role_rewards` - Disables gaining role rewards based on levels (if the guild has it enabled)

You can use the following commands to view/change your own settings:

- **config user list** - Show your config
- **config user edit** `<setting_key>` `<setting_value>` - Update a value in your user config

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

* **config log list** - Look at the log config
* **config log edit** `key` `value` - Enable or disable one of the events

## Moderation

We have some commands which can help moderate the server, or improve other things!

### Channels

You can set flags for channels, the no_xp flag can also be used for voice channels!

##### Flags

* `no_xp` - Bot does not count xp for users when they send messages in this channel
* `media_only` - Channel allows only media and URL, anything else will be deleted.
* `no_stickers` - Stickers will be removed from the chat
* `no_log` - Message logging is disabled for this channel

##### Commands

* **channels list** - List of channels with flags set
* **channels flag** `channel` `flag` - add flag to a channel
* **channels unflag** `channel` `flag` - remove flag from a channel

### Timeout detection

We are not satisfied with the audit log and how timeouts are displayed and
filtered. It is not easy to quickly look up how often somebody has been timed
out, for what reason and how long. Every timeout is automatically saved
in the database including the reason and duration. We can easily see a history
of timeouts + filter timeouts only for specific users.

* **timeouts list** `<user_mention>` - All timeouts or those from a specific user
* **timeouts edit** `<timeout_id>` `<new_reason>`- Update the reason for a timeout
* **timeouts delete** `<timeout_id>` - Remove a timeout from the log only

### Blacklist

People can be put on a blacklist manually or automatically.

* **blacklist list** - Show everyone on the blacklist
* **blacklist block** `<user_mention` `<reason>` - Add someone to the blacklist
* **blacklist unblock** `<user_mention>`  - Remove someone from the blacklist

### Join role

You can make sure the bot gives users a role when they join the server. In order to make it work
you have to set the following settings:

* `enable_join_role` - Enable or disable giving the role
* `join_role` - ID of the actual role to give

### Disable server invites

Since discord requires a "Mange server" permission to pause invites I added a simple command to
do it with the bot:

- **invites toggle** - Enable/disable invites

### Custom messages

When adding custom messages you can use the `:user` parameter in you message to be replaced with a tag of the user,
for level up messages you can also use `:level`. Example:

* `Welcome :user!`
* `Congrats :user you are now :level!`

#### Welcome

You can add custom welcome messages to the bot. It wil pick one of the random messages and sends it in the channel
set in the config.

* **messages welcome list** - Show list of custom level up messages
* **messages welcome add**  `<message>` - Add new level up message
* **messages welcome delete** `<message>` - Delete level up message (Message is autocompleted)

#### Levels

You can set custom messages when users level up to a certain level. If you don't set any, a default message will
be used if level up messages are enabled. When a user levels up, it checks for the highest message in the list
which is not higher than that users level. Meaning if you set 2 messages, one for level 1 and one for level 10.
The level 1 message will be used until that user reached level 10.

* **messages levels list** - Show list of custom level up messages
* **messages levels add** `<level>` `<message>` - Add new level up message
* **messages levels delete** `<level>` - Delete level up message

## Mention Responder

Small funny feature, when you tag the bot you will get a random reply from a list of mention replies. There are default
replies, but you can also add your own replies based on certain roles or users in the server.

The bot comes with some default groups and replies:

- If you have a high rank in the server (according to the xp system)
- If you are the highest person on the leaderboard (according to the xp system)
- If you bumped the discord the most (all time)
- If you bumped the discord a lot
- If you had timeouts in the past
- If you are highly ranked on the cringe counter leaderboard

You can manage all groups and their replies by using these commands:

- **mention groups list** - Show all groups
- **mention groups add** `discord_role` - Add a group
- **mention groups delete** `group_id` - Delete a group and its replies (!!!)
- **mention replies** `group_id` - Show all replies for a single group
- **mention replies add** `group_id` `reply_line` - Add a reply to a group
- **mention replies delete** `reply_id`- Delete a reply

## Fun commands

A few fun commands you can use!

* **fun urb** `search_term` - Searches on urban dictionary
* **fun 8ball** `question` - Ask a question to the 8ball
* **fun ask** `question` - Ask a question and get a gif response
* **fun say** `something` - say something
* **fun modstats** - Who got the power?
* **fun emotes** - List of most used emotes in the guild
* **fun bumpcounter** `time-range` • Check the monthly or all time bump leaderboard
* **cringe add** `<user>` - Increase someone's cringe counter
* **cringe delete** `<user>` - Decrease someone's cringe counter
* **cringe reset** - Reset someone's cringe counter

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

* **reactions list**
* **reactions add** `<word_trigger` `<reaction_emote>`
* **reactions delete** `<word_trigger`

### Simple commands

Simple commands such as $ping -> response pong can be added in discord as well.
Same as with the reactions you can add and remove and view as many as you like
without the necessity to enter any code. By default, these command triggers do not include the bot
prefix, so if you want to trigger on prefix you need to include the bot prefix in the command.

* **commands list**
* **commands add** `<command>` `<response>`
* **commands delete** `<command>`

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
