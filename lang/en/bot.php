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
        'added' => 'User added',
        'not-exist' => 'User does not exist',
        'powerful' => ':name is to powerful for you',
        'deleted' => 'User Deleted',
        'changed' => 'User level changed'
    ],

    'bump' => [
        'inc' => ":name heeft :count x de discord gebumped!",
        'footer' => 'Use /bump in #botspam',
        'title' => 'Bumper Elites',
        'description' => "Bump counter\n\n :bumpers"
    ],

    'cringe' => [
        'footer' => 'usage: addcringe, delcringe, cringecounter',
        'title' => 'Cringe Counter',
        'description' => "List of the most cringe people in our discord! \n\n :users",
        'count' => "Cringe counter for :name is :count",
        'change' => "Cringe counter for :name is now :count",
        'not-cringe' => ":name is not cringe",
    ],

    'cmd' => [
        'saved' => 'Command saved',
        'deleted' => 'Command deleted',
        'footer' => 'usage: addcmd, delcmd, commands',
        'title' => 'Commands',
        'description' => "Basic text commands. \n\n :cmds",
    ],

    'reactions' => [
        'saved' => 'Reaction saved',
        'deleted' => 'Reaction deleted',
        'footer' => 'usage: addreaction, delreaction, reactions',
        'title' => 'Reactions',
        'description' => "Basic reactions. \n\n :reactions",
    ],

    'timeout' => [
        'footer' => 'Timeouts given through discord are automatically added',
        'title' => 'Timeouts',
        'count' => "Total timeouts: :count"
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
    ],

    'help' => [
        'title' => 'BerendBotje Commands',
        'footer' => 'Bot written by berend using DiscordPHP.',
    ],
];
