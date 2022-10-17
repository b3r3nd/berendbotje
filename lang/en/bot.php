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
    ]
];
