# Introdcution
Discord bot written in Laravel using Discord offical PHP library
http://discord-php.github.io/DiscordPHP

Create for our discord server at http://discord.gg/netherlands.

For now the code only runs on a single server and multi server support 
is not yet included. Many more things will be added in the future but 
its a hobby project so I take my time :)

# Functions
Bot is still early in development and not many features are added. 
Also some of the code is pretty nasty still and there is redundancy in
the database (Users and their IDs for example). 

## Bump Counter
We use a bot to add our discord server to an external website, once every
2 hours you can use this bot to get back on the front page. To encourage
people in our server to use the bump our bot counts for every member how often
they bump. At the end of the month we will check the highest member and he
or she will get some nice perks!

## Timeout detection
We are not satisfied with the audit log and how timeouts are displayed and
filtered. It is not easy to quickly look up how often somebody has been timed
out, for what reason and how long. Every timeout is automatically saved
in the database including the reason and duration. We can easily see a history
of timeouts + filter timeouts only for specific users.

### Admin management
The bot works with access levels between 1 and 1000. Users can be added to
the bot and have access to commands corresponding to their level. What 
access level is required for a command of function is also dynamic and can 
be changed in order to give admins freedom of choice what levels can use w
which commands.

### Reactions
Certain strings can be added to the bot and when those strings are detected
in a message the ot will add a reaction to the message with a set emote. These
reactions can be added, removed and viewed with commands so nothing needs to be
done in code.

### Simple commands
Simple commands such as $ping -> response pong can be added in discord as well.
Same as with the reactions you can add and remove and view as many as you like
without the necessity to enter any code. 

### Cringe Counter
Sometimes people on discord can be quite cringe, some more than others.
We can add cringe counters to member to see who makes the most cringe 
comments. Just a funny little feature!

# Commands
For now the following commands are implemented
* admins
* addadmin
* deladmin
* clvladmin
* addreaction
* delreaction
* reactions
* addcmd
* delcmd
* commands
* timeouts
* timeouts discord_tag
* bumpstats
* addcringe
* cringe
* cringestats

Custom commands added with the addcmd which only gives text responses are 
dynamic and thus not added to the list.


# Questions
If you have any questions you can join our discord at http://discord.gg/netherlands
or contact me directly through discord berend#0579.
