# Introdcution

Discord bot written in PHP, using laravel and the DiscordPHP package. Initially created just for our own server but
now runs on as many serves as you like. If you have questions DM my directly by my discord tag: `berend#0579`.

## Requirements

* Linux machine!
* Configured mysql database and knowing the credentials
* Bot signed up to developers portal and knowing your bot token
* PHP 8.0+
* Composer to install dependencies

## Installing

1. Download this repo to your linux server/pc.
2. Run `composer install` to install all its dependencies
3. Create the .env file using `mv .env.example .env`
4. In the `.env` file fill in your database credentials!
5. `BOT_TOKEN` is already added to the .env example, fill in your bot token there!
6. Install FFMPEG using `sudo apt-get install ffmpeg` or however your package manager works.
7. Install `youtube-dl` following the installation guide in their
   repo https://github.com/ytdl-org/youtube-dl#installation
8. There is a seeder file called `database\seeders\DiscordUsersSeeder`, here you can add your user id and server id to
   setup admin account with 1000 access to a single server.
9. Cd to your project directory and use `php artisan migrate --seed` to setup your database.

## Running the bot

1. I added an artisan command to run the bot `php artisan bot:run`.
2. In order to make the music player work you need to run the redis queue as well `php artisan queue:work`
3. Make sure both these run in the background while you exit your connection / terminal. Or keep two terminals running
   if you want to test it locally.

# Functions

I will try to update this readme with new functionality as I add it, but I cannot promise I keep it entirely up to date.
The bot only uses slash commands, there used to be message commands but I moved everything to slash only.

## Multiple servers
The bot runs on multiple servers, but each server requires some settings and values to be set in the database, 
the plan is to let the bot do it automatically whenever he is invited and joins a server. For now it needs to be 
done manually or added to the seeders.

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
* `media-filter`
* `add-cringe`
* `delete-cringe`
* `commands`
* `reactions`
* `role-rewards`
* `manage-xp`

### Default Roles

* **Admin**
    * All permissions
* **Moderator**
    * `timeouts`
    * `media-filter`
    * `add-cringe`
    * `delete-cringe`
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
at all.
It is possible to attach role rewards to levels, when a user reaches that specific level a new role is granted.
### Calculating Levels
This is what I use to calculate required XP for each level: `XP = 500 * (level^2) - (500 * level)`

Example for level 5: `500 * (5^2) - (500 * 5) = 10.000 XP` 


### Commands
* **leaderboard** • Show the leaderboard with the highest ranking members at the top
* **rank** • Show your own level, xp and messages
* **givexp** `user_mention` `xp_amount` • Give user xp
* **removexp** `user_mention` `xp_amount` • Remove user xp
* **resetxp** `user_mention` • Reset XP for user
* **rewards** • Show the role rewards for different levels
* **addreward** `level` `role_id` • Add a role reward to a level
* **delreward** `level` • Delete role rewards from this level";

## Bot Config

The bot loads a config from the settings table which can be viewed and changed, it allows anything to be changed to
anything
you want so make sure you know what you are doing.

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

## Logging
The bot is able to log some events to a specific log channel, check the bot config. Enable the logging and set a proper channel.
Following events are logged:
- Joined server
- Left server
- Kicked from server
- Banned from server
- Received timeout
- Joined voice call
- Left voice call
- Updated username (old and new username)
- Message updated (show old and new message)
- Message deleted (show deleted message)

## Channels
You can set flags for channels, for now there are two flags you can use:
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

## Youtube music player (currenlty broken)

The bot has a simple music player with queue. I am using laravel queues to
download the songs from youtube in the background. They are added to the queue
and once you use the $play command it will play the entire queue and then leave the call.
Songs obviously can be added while the bot is playing. Once a song is downloaded the job in
the queue will create an entry in the songs table, that's the queue. It will delete the entry
and file once the song has been played! The libraries I used to both to download and
play songs are included at the top.

Commands are:

* **addsong** `<youtube_url>`
* **play**
* **stop**
* **pause**
* **resume**
* **queue**

## Bump Counter

We use a bot to add our discord server to an external website, once every
2 hours you can use this bot to get back on the front page. To encourage
people in our server to use the bump our bot counts for every member how often
they bump. At the end of the month we will check the highest member and he
or she will get some nice perks!

Command to view the bump statistics is

* **bumpcounter**

## Cringe Counter

Sometimes people on discord can be quite cringe, some more than others.
We can add cringe counters to member to see who makes the most cringe
comments. Just a funny little feature! You can only add and delete a single cringe
at the time. No deleting everything at once!

commands to use cringe is:

* **addcringe** `<user>`
* **delcringe** `<user>`
* **cringecounter**

## Fun commands

A few fun commands you can use

* **urb** `search_term` - Searches on urban dictionary
* **8ball** `question` - Ask a question to the 8ball
* **ask** `question` - Ask a question and get a gif response
* **say** `something` - say something

That's it for now! Enjoy! :)
