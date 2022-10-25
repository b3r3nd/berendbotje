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

    'admins' => [
        'index' => 'Admins',
        'title' => 'Admins',
        'footer' => 'Usage: admins, addadmin, deladmin, clvladmin',
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
        'description' => "Basic reactions. \n\n :reactions",
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
        'footer' => 'Usage: addsong, play, queue, stop. pause, resume',
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
    ],

    'help' => [
        'title' => 'BerendBotje Commands',
        'footer' => 'Bot written by berend using DiscordPHP.',
    ],

    'say-usage' => "Usage: say `text_string`",
];
