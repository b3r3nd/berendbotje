# Introdcution
Discord bot written in Laravel using:
* Offical PHP library http://discord-php.github.io/DiscordPHP
* To download from youtube https://github.com/ytdl-org/youtube-dl
* Play audio https://ffmpeg.org/
* Redis for queues https://github.com/predis/predis

Created for our discord server at http://discord.gg/netherlands. If you have questions you can join the server and 
contact me in the `#developers` channel or DM my directly by my tag: `berend#0579` For now the code only runs on a single server and multi server support
is not yet included. Many more things will be added in the future but
its a hobby project so I take my time :)
## Requirements
Before you use the bot, you require the following:
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
8. Cd to your project directory and use `php artisan migrate` to setup your database.
9. There are no seeders, so there is no data. Some commands require bot access 1000. As a one time setup you need to manually add your discord id etc to the admins table with 1000 access in order to use the bot and add more admins.

## Running the bot
1. I added an artisan command to run the bot `php artisan bot:run`.
2. In order to make the music player work you need to run the redis queue as well `php artisan queue:work`
3. Make sure both these run in the background while you exit your connection / terminal. Or keep two terminals running if you want to test it locally.

# Functions
Bot is still early in development and not many features are added. Some code is still pretty nasty so bear with me!

**The trigger for the bot and all its commands is $ but can be easily changed. Most commands also work as 
slash commands!**

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

## Cringe Counter
Sometimes people on discord can be quite cringe, some more than others.
We can add cringe counters to member to see who makes the most cringe 
comments. Just a funny little feature! You can only add and delete a single cringe
at the time. No deleting everything at once!

commands to use cringe is: 
* **addcringe** `<user>`
* **delcringe** `<user>`
* **cringecounter**

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

## Admin management
The bot works with access levels between 1 and 1000. Users can be added to
the bot and have access to commands corresponding to their level. What
access level is required for a command of function is also dynamic and can
be changed in order to give admins freedom of choice what levels can use w
which commands.

There are 3 levels predefined in the bot:
* **Admin** -> 1000
* **Mod** -> 500
* **User** -> 100

Commands have their access level defined using the Enum class in each command child. Commands to manage admins are:
* **admins**
* **addadmin** `<user_mention>` `<access_level>`
* **deladmin** `<user>`
* **clvladmin** `<user>` `<access_level>`


Thats it for now! I will keep updating this readme everytime I add new functionality :)
