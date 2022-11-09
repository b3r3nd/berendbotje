<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Discord bot Language files
    |--------------------------------------------------------------------------
    |
    | The following language lines are used throughout the bot as command responses
    | and index pages!
    */

    'status' => 'All of you...',
    'provide-args' => 'Provide arguments noOo0Oo0Ob',
    'provide-arguments' => 'This command requires :count arguments to be given',
    'provide-mention' => 'This command requires a user to be mentioned',
    'lack-access' => 'You lack access to use this command',
    'error' => 'Error',
    'done' => 'Done!',
    'media-deleted' => 'Your message in :channel has been deleted. Only media and URLs are allowed.',
    'no-term' => 'Enter a search term',
    'no-valid-term' => 'Search term :term cannot be found',
    'needhelp' => 'For more info check $help',


    'myroles' => [
        'title' => 'Roles in this server',
        'footer' => 'See help for more info',
        'description' => ':roles',
        'none' => 'No roles in this server',
    ],

    'roles' => [
        'title' => 'Roles for this server',
        'footer' => 'see help for more info',
        'description' => ':roles',
        'usage-addrole' => 'addrole `role_name`',
        'usage-delrole' => 'delrole `role_name`',
        'exist' => 'Role already exists',
        'created' => 'Role :role created',
        'not-exist' => 'Role :role does not exist',
        'deleted' => 'Role :role deleted',
        'usage-attachperm' => 'addperm `role_name` `perm_name`',
        'usage-attachrole' => 'adduser `user_mention` `role_name`',
        'usage-detachperm' => 'delperm `role_name` `perm_name`',
        'usage-detachrole' => 'deluser `user_mention` `role_name`',
        'perm-attached' => 'Permission :perm given to role :role',
        'role-attached' => 'Role :role given to user :user',
        'perm-detached' => 'Permission :perm removed from role :role',
        'role-detached' => 'Role :role removed from user :user',
        'has-users' => 'You cannot delete roles in use by users, remove users first.',
        'usage-userroles' => 'userroles `user_mention`'
    ],

    'permissions' => [
        'title' => 'Global permissions',
        'footer' => 'See help for more info',
        'description' => ':perms',
        'not-exist' => 'Permission :perm does not exist',
    ],


    'server' => [
        'usage-addserver' => 'addserver `server_id` `owner_user_id`',
        'added' => 'server with ID: :id added to owner :owner',
        'title' => 'Active servers',
        'footer' => 'user addserver server_id discord_owner_id to add new servers',
        'description' => "Server • Owner Account\n\n:servers"
    ],

    '8ball' => [
        'no-question' => 'You should ask me a question..',
    ],

    'xp' => [
        'not-found' => 'You do not have any messages',
        'count' => 'You have :messages',
        'footer' => 'You gain :xp per message',
        'title' => 'You are level :level',
        'description' => "You have :messages messages which amounts to :xp xp!",
    ],

    'set' => [
        'footer' => 'Use set <setting_key> <new_value> to update settings.',
        'title' => 'General bot settings',
        'usage-set' => 'Usage: set `setting_key` `setting_value`',
        'not-exist' => 'Setting :key does not exist',
        'updated' => 'Setting :key is updated to value :value',
    ],

    'messages' => [
        'title' => 'xp and level statistics',
        'footer' => 'You gain :xp xp per message',
        'description' => "List of messages and xp for users\n\n:users",
    ],

    'media' => [
        'usage-addmedia' => 'Usage: addmediachannel `channel`',
        'usage-delmedia' => 'Usage: delmediachannel `channel`',
        'added' => 'Media channel :channel added',
        'deleted' => 'Media channel :channel deleted',
        'exists' => 'Channel :channel is already marked as media channel',
        'not-exists' => 'Channel :channel is not marked as media channel',
        'no-channel' => 'Please provide a valid channel.',
        'title' => 'Media Channels',
        'footer' => 'Usage: addmediachannel, delmediachannel, mediachannels',
        'description' => "Channels marked as media only allow attachments and URLS\n\n:channels"
    ],

    'buttons' => [
        'next' => 'Next Page',
        'previous' => 'Previous Page'
    ],

    'admins' => [
        'index' => 'Admins',
        'title' => 'Admins',
        'footer' => 'Usage: admins, access, addadmin, deladmin, clvladmin',
        'description' => "List of bot administrators\n\n :admins",
        'exists' => 'User already exists, you can change level with clvladmin',
        'provide-access' => "Provide access level..",
        'lack-access' => "Can't give more access than you have yourself..",
        'added' => 'User :user added with access level :level',
        'not-exist' => 'User does not exist',
        'powerful' => ':name is to powerful for you',
        'deleted' => 'User :user Deleted',
        'changed' => 'User :user level changed to :level',
        'usage-addadmin' => 'Usage: addadmin `user_mention` `user_level`',
        'usage-deladmin' => 'Usage: deladmin `user_mention`',
        'usage-clvladmin' => 'Usage: clvladmin `user_mention` `new_access_level`',
        'desc-addadmin' => 'Add an administrator to the bot',
        'desc-deladmin' => 'Delete an administrator from the bot',
        'desc-clvladmin' => 'Change the level from an administrator',
        'desc-index' => 'Show all the bot administrators',
    ],

    'adminstats' => [
        'title' => 'Moderator statistics',
        'footer' => 'Counts bans, kicks and timeouts.',
        'description' => "Who got the power?\n\n",
    ],

    'access' => [
        'title' => 'Your access level',
        'footer' => 'To see all admins use $admins',
        'desc' => 'Your access level to the bot is :level',
        'desc-lack' => 'You do not have any access to the bot.'

    ],

    'bump' => [
        'inc' => ":name heeft :count x de discord gebumped!",
        'footer' => 'Use /bump in #botspam',
        'title' => 'Bumper Elites',
        'description' => "Bump counter\n\n:bumpers"
    ],

    'cringe' => [
        'footer' => 'usage: addcringe, delcringe, cringecounter',
        'title' => 'Cringe Counter',
        'description' => "List of the most cringe people in our discord! \n\n:users",
        'count' => "Cringe counter for :name is :count",
        'change' => "Cringe counter for :name is now :count",
        'not-cringe' => ":name is not cringe",
        'usage-delcringe' => "Usage: delcringe `user_mention`",
        'usage-addcringe' => "Usage: addcringe `user_mention`",
        'usage-resetcringe' => "Usage: resetcringe `user_mention`",
        'reset' => "Cringe for :user is reset to 0",
    ],

    'cmd' => [
        'saved' => 'Command :trigger saved with response :response',
        'deleted' => 'Command :trigger deleted',
        'footer' => 'usage: addcmd, delcmd, commands',
        'title' => 'Commands',
        'description' => "Basic text commands. \n\n :cmds",
        'usage-addcmd' => "Usage: addcmd `command_trigger` `response`",
        'usage-delcmd' => "Usage: addcmd `command_trigger`",
    ],

    'reactions' => [
        'saved' => 'Reaction :reaction on :name saved',
        'deleted' => 'Reaction for :name deleted',
        'footer' => 'usage: addreaction, delreaction, reactions',
        'title' => 'Reactions',
        'description' => "Basic reactions.",
        'usage-addreaction' => 'Usage: addreaction `reaction_trigger` `reaction_emote`',
        'usage-delreaction' => 'Usage: delreaction `reaction_trigger`',
    ],

    'timeout' => [
        'footer' => 'Timeouts given through discord are automatically added',
        'title' => 'Timeouts',
        'count' => "Total timeouts: :count",
        'usage-timeouts' => 'Usage: usertimeouts `user_mention`',
    ],

    'music' => [
        'title' => 'Music Queue',
        'footer' => 'Usage: addsong, removesong, play, queue, stop, pause, resume',
        'player-status' => 'Player is :status',
        'add-to-queue' => 'Music is playing.. adding to queue..',
        'added' => 'Song added to queue',
        'starting' => 'Starting player..',
        'resuming' => 'Resuming player..',
        'already-playing' => 'Player already playing',
        'pausing' => 'Pausing player..',
        'already-paused' => 'Player already paused',
        'stopping' => 'Stopping player..',
        'already-stopped' => 'Player already stopped',
        'no-music' => 'No music in queue to play, use addsong to add songs',
        'usage-addsong' => "Usage: addsong `youtube_url`",
        'usage-remove' => 'Usage: removesong `queue_id`',
        'removed-song' => 'Removed song from the queue',
        'not-found' => 'Song with that ID is not in queue',
    ],

    'emotes' => [
        'title' => 'Emote Counter',
        'footer' => 'Counts all used emotes!',
        'description' => "List of most used emotes\n\n:emotes",
    ],

    'help' => [
        'title' => 'BerendBotje Commands',
        'footer' => 'Bot written by berend using DiscordPHP.',
    ],

    'say-usage' => "Usage: say `text_string`",
];
