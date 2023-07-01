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
    'lack-access' => 'You do not have permission to run this command. Use `/role` to check your roles in this server.',
    'error' => 'Error',
    'done' => 'Success',
    'media-deleted' => 'Your message in :channel has been deleted. Only media and URLs are allowed.',
    'no-term' => 'Enter a search term',
    'no-valid-term' => 'Search term :term cannot be found',
    'needhelp' => 'For more info check /help',
    'bump-reminder' => 'BUMP TIME :role',

    'permissions-enum' => [
        'roles' => 'Roles',
        'create-role' => 'Create role',
        'delete-role' => 'Delete role',
        'update-role' => 'Update role',
        'permissions' => 'Permissions',
        'attach-permission' => 'Update permission from role',
        'attach-role' => 'Update roles from user',
        'config' => 'See and update the config',
        'timeouts' => 'See timeout history',
        'add-cringe' => 'Increase the cringe counter',
        'delete-cringe' => 'Decrease the cringe counter',
        'commands' => 'Create and delete custom commands',
        'reactions' => 'Create and delete custom reactions',
        'role-rewards' => 'Create and update role rewards',
        'manage-xp' => 'Modify XP for users',
        'channels' => 'Set and update channel flags',
        'logs' => 'Update the log config',
        'add-mention' => 'Add new mention replies',
        'delete-mention' => 'Remove mention replies',
        'manage-mention-groups' => 'Manage mention reply groups',
        'media-filter' => 'Media filter',
        'openai' => 'Use OpenAi Commands',
        'abusers' => 'Manage the blacklist',
    ],

    'slash' => [
        'blacklist' => 'Show the user blacklist',
        'block' => 'Add user to the blacklist',
        'unblock' => 'Remove user from the blacklist',
        'roles' => 'Show all roles in the server',
        'users' => 'Show all users with their roles',
        'permissions' => 'Show all available permissions',
        'servers' => 'Show all servers the bot runs on',
        'myroles' => 'Show your roles in this server',
        'userroles' => 'Show roles of yourself or given user',
        'delete-role' => 'Delete a role from the server',
        'create-role' => 'Add a new role to the server',
        'detach-role-perm' => 'Remove permissions from role',
        'attach-role-perm' => 'Add permissions to role',
        'attach-user-role' => 'Add user to role',
        'detach-user-role' => 'Remove user from role',
        'set' => 'Update setting from config',
        'config' => 'Show the server configuration',
        'user-timeouts' => 'Show timeouts for single user',
        'timeouts' => 'Show all timeouts or by single user',
        'modstats' => 'Show moderator statistics',
        'leaderboard' => 'Show leaderboard with user levels',
        'rank' => 'Show your own level and xp',
        'rewards' => 'Show role rewards based on levels',
        'add-role-reward' => 'Add role reward to a level',
        'del-role-reward' => 'Remove role rewards from a level',
        'give-xp' => 'Give xp to a user',
        'remove-xp' => 'Remove xp from a user',
        'reset-xp' => 'Reset xp for a user',
        'cringecounter' => 'Show who is most cringe..',
        'inc-cringe' => 'Increase the cringe counter by one for someone',
        'dec-cringe' => 'Decrease the cringe counter by one for someone',
        'reset-cringe' => 'Reset the cringe counter for someone',
        'bumpstats' => 'Show bumper elite statistics',
        'emotes' => 'Show emote counter',
        'commands' => 'Show list of custom commands',
        'reactions' => 'Show list of custom reactions',
        'add-command' => 'Add a new custom command',
        'del-command' => 'Delete a custom command',
        'add-reaction' => 'Add a new custom reaction',
        'del-reaction' => 'Delete a custom reaction',
        '8ball' => 'Ask the magic 8ball for advise',
        'ask' => 'Ask a yes or no question',
        'urb' => 'Search on urban dictionary',
        'help' => 'Help files with optional parameters',
        'channels' => 'Overview of all channels and their flags',
        'mark-channel' => 'Add flags to a channel',
        'unmark-channel' => 'Remove flags from a channel',
        'logconfig' => 'Configuration for log channel',
        'logset' => 'Enable or disable parts of the log',
        'mentionindex' => 'List of all replies used by the mention responder',
        'addgroup ' => 'Create a new mention group',
        'delgroup' => 'Delete a mention group',
        'updategroup' => 'Update a mention group',
        'addreply' => 'Add a new reply to a mention group',
        'delreply' => 'Delete a new reply from a mention group',
        'searchreply ' => 'Search for a reply',
        'mentiongroups' => 'Mention responder group',
    ],

    'blacklist' => [
        'title' => 'User Blacklist',
        'block' => ":user added to blacklist.",
        'unblock' => ":user had been deleted from the blacklist",
        'blocked' => ':user is already on the blacklist',
        'unblocked' => ':user is not on the blacklist'
    ],

    'channels' => [
        'added' => 'Channel flag :flag added to channel <#:channel>',
        'deleted' => 'Channel flag :flag removed from channel <#:channel>',
        'has-flag' => 'Channel :channel is already marked with that flag',
        'no-flag' => 'Channel :channel is not marked with that flag',
        'no-channel' => 'Please provide a valid channel.',
        'title' => 'ChannelFlags',
        'footer' => 'Usage: markchannel, unmarkchannel, channels',
        'description' => "ChannelFlags with their flags, see `/help` for more information about what each flag means.\n\n:channels"
    ],

    'logset' => [
        'updated' => 'Log setting :key updated',
        'title' => 'Log Settings',
        'footer' => 'Usage: logconfig, logset',
    ],


    'userroles' => [
        'title' => 'Roles in this server',
        'footer' => 'See help for more info',
        'description' => "Roles for :user \n\n :roles",
        'none' => ':user has no roles in this server',
    ],

    'users' => [
        'title' => 'Users for this server',
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
        'usage-userroles' => 'userroles `user_mention`',
        'admin-role' => 'Cannot delete administrator role',
        'admin-role-perms' => 'You cannot remove permissions from the main administrator role',
        'admin-role-owner' => 'You cannot remove the owner from the list of admins',
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
        'description' => "Server • Owner Account\n\n:servers",
    ],

    '8ball' => [
        'no-question' => 'You should ask me a question..',
    ],

    'rewards' => [
        'title' => 'Role rewards',
        'footer' => 'Use help for more information',
        'description' => "Level • Role Reward\n\n:rewards",
        'usage-delreward' => 'Usage: delreward `level`',
        'usage-addreward' => 'Usage: addreward `level`, `role_id`',
        'added' => 'Role reward :role added for level :level',
        'deleted' => 'All role rewards for :level deleted',
        'number' => 'Both level and role ID need to be numeric.'
    ],

    'xp' => [
        'not-found' => ':user does not have any messages',
        'count' => 'You have :messages',
        'footer' => ':xp xp per message',
        'title' => 'Level :level',
        'description' => "User: :user\nLevel: :level \nXP: :xp\nMessages: :messages \nVoice: :voice",
        'usage-givexp' => 'givexp `user_mention` `xp_amount`',
        'usage-delxp' => 'removexp `user_mention` `xp_amount`',
        'usage-resetxp' => 'resetxp `user_mention`',
        'given' => ':xp xp given to <@:user>',
        'removed' => ':xp xp removed from <@:user>',
        'reset' => 'xp for <@:user> is reset',
        'not-exist' => 'User <@:user> has no messages or experience in this server'
    ],

    'set' => [
        'footer' => 'Use set <setting_key> <new_value> to update settings.',
        'title' => 'General bot settings',
        'usage-set' => 'Usage: set `setting_key` `setting_value`',
        'not-exist' => 'Setting :key does not exist',
        'updated' => 'Setting :key is updated to value :value',
        'not-numeric' => 'Setting values must be numeric, :value is not a numeric value',
    ],

    'messages' => [
        'title' => 'xp and level statistics',
        'footer' => 'You gain :xp xp per message',
        'description' => "List of messages and xp for users\n\n:users",
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
        'inc' => ":name has bumped this discord :count times!",
        'footer' => 'Use /bump in #bump',
        'title' => 'Bump Elites',
        'description' => "Bump counters of all time!\n\n:bumpers",
        'description-month' => "Bump counters of this month!\n\n:bumpers",
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
        'fail' => "Nice try noob, I increased your cringe counter instead. Count is now :count"
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
        'usage-timeouts' => 'Usage: timeouts `user_mention`',
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
        'title' => 'Help',
        'footer' => 'Switch between sections below',
    ],

    'say-usage' => "Usage: say `text_string`",


    'mention' => [
        'title' => 'Mention Responses',
        'footer' => 'List of mention groups and their responses',
        'description' => ':data',
        'added' => "**Reply:** \n :reply \n\n **Group:** :group",
        'deleted' => 'Reply has been deleted',
        'no-group' => 'Group not found, use the group ID',
        'no-reply' => 'Reply not found, use the reply ID',
    ],
    'mentiongroup' => [
        'title' => 'Mention Groups',
        'footer' => 'List of mention groups',
        'description' => ':data',
        'added' => 'Mention group :group added',
        'deleted' => 'Mention group and all of its replies deleted',
        'not-found' => 'Group with id :ID not found',
        'integer' => 'A group must be the ID of a server role! (for now)',
        'notexist' => 'No mention group found for id :group',
        'updated' => 'Mention group updated'
    ]
];
