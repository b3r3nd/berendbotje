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
7. Install `youtube-dl` following the installation guide in their repo https://github.com/ytdl-org/youtube-dl#installation
8. There is a seeder file called `database\seeders\DiscordUsersSeeder`, here you can add your user id and server id to setup admin account with 1000 access to a single server.
9. Cd to your project directory and use `php artisan migrate --seed` to setup your database.

## Running the bot
1. I added an artisan command to run the bot `php artisan bot:run`.
2. In order to make the music player work you need to run the redis queue as well `php artisan queue:work`
3. Make sure both these run in the background while you exit your connection / terminal. Or keep two terminals running if you want to test it locally.

# Functions
I will try to update this readme with new functionality as I add it, but I cannot promise I keep it entirely up to date.
The trigger for all commands is `$` and for the majority of them slash commands are also enabled.

## Bot Config
The bot loads a config from the settings table which can be viewed and changed, it allows anything to be changed to anything
you want so make sure you know what you are doing. 

* **config**
* **set** `setting_name` `new_value`

For now there are only two settings:
* `xp_count` - xp gained per message
* `xp_cooldown` - seconds cooldown after last message before it counts again

## Adding to multiple servers
The bot works on multiple servers, if you want to add it to another server use the following command:
* **addserver** `server_id` `owner_discord_id`
## Admin management
The bot works with access levels between 1 and 1000. Users can be added to
the bot and have access to commands corresponding to their level. What
access level is required for a command is now coded into the bot per command. In the future I will make it entirely
dynamic, so you can change the levels required for each command per server.

For now there are 5 levels predefined in the bot:
* **Bot owner** -> 1000
* **Server owner** -> 900
* **Server Admin** -> 800
* **Mod** -> 500
* **User** -> 100

Commands to manage admins are:
* **admins**
* **addadmin** `<user_mention>` `<access_level>`
* **deladmin** `<user>`
* **clvladmin** `<user>` `<access_level>`

## Media channels
You can add channels to a list of channels marked as media only, in these channels only attachments and (https) urls are allowed.
Anything else will be deleted!

* **mediachannels**
* **addmediachannel** `channel`
* **delmediachannel** `channel`

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

## Message counter
The bot counts messages and gives XP for each message. There are two settings which allow you to change the amount
of XP gained and the cooldown for when to count messages. For example only one message a minute counts.
* **messages**
* **xp**

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

## Youtube music player
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
