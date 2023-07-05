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

    'lack-access' => 'You do not have permission to run this command. Use `/role` to check your roles in this server.',
    'error' => 'Error',
    'done' => 'Success',
    'media-deleted' => 'Your message in :channel has been deleted. Only media and URLs are allowed.',
    'no-valid-term' => 'Search term :term cannot be found',
    'bump-reminder' => 'BUMP TIME :role',
    'monthly' => 'Montly',
    'all-time' => 'All time',
    'command' => 'Command',
    'response' => 'Response',
    'trigger' => 'Trigger',
    'reaction' => 'Reaction',
    'user-mention' => 'User Mention',
    'question' => 'Question',
    'search-term' => 'Search Term',
    'cannot-count' => "Can't count :)",
    'wrong-number' => 'Wrong numer, reset to :count',
    'level-up-msg' => 'Hey strijder <@:user>, je bent nu level :level, gefeliciteerd! Berend zal je nu op een drankje trakteren!',
    'level' => 'Level',
    'role' => 'Role',
    'user' => 'User',
    'key' => 'Key',
    'group' => 'Group',
    'value' => 'Value',
    'role-or-user-id' => 'Role or User ID',
    'role-or-user' => 'Is it a user or role?',
    'multiplier' => 'Usage Mutliplier',
    'group-id' => 'Group ID',
    'reply' => 'Reply',
    'reply-id' => 'Reply ID',
    'reason' => 'Reason',
    'channel' => 'Channel',
    'flags' => 'Flags',
    'permission' => 'Permission',
    'date-range' => 'Date Range',

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
        'userset' => 'Update user specific settings for this guild',
        'userconfig' => 'User specific settings for this guild',
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

    'userconfig' => [
        'title' => 'User settings',
        'not-found' => 'No user settings found, see `/help` -> user settings for more info.',
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
        'description' => "ChannelFlags with their flags, see `/help` for more information about what each flag means.\n\n:channels"
    ],

    'logset' => [
        'updated' => 'Log setting :key updated',
        'title' => 'Log Settings',
    ],


    'userroles' => [
        'title' => 'Roles in this server',
        'description' => "Roles for :user \n\n :roles",
        'none' => ':user has no roles in this server',
    ],

    'users' => [
        'title' => 'Users for this server',
    ],

    'roles' => [
        'title' => 'Roles for this server',
        'description' => ':roles',
        'exist' => 'Role already exists',
        'created' => 'Role :role created',
        'not-exist' => 'Role :role does not exist',
        'deleted' => 'Role :role deleted',
        'perm-attached' => 'Permission :perm given to role :role',
        'role-attached' => 'Role :role given to user :user',
        'perm-detached' => 'Permission :perm removed from role :role',
        'role-detached' => 'Role :role removed from user :user',
        'has-users' => 'You cannot delete roles in use by users, remove users first.',
        'admin-role' => 'Cannot delete administrator role',
        'admin-role-perms' => 'You cannot remove permissions from the main administrator role',
        'admin-role-owner' => 'You cannot remove the owner from the list of admins',
    ],

    'permissions' => [
        'title' => 'Global permissions',
        'description' => ':perms',
        'not-exist' => 'Permission :perm does not exist',
    ],

    '8ball' => [
        'no-question' => 'You should ask me a question..',
    ],

    'rewards' => [
        'title' => 'Role rewards',
        'description' => "Level • Role Reward\n\n:rewards",
        'added' => 'Role reward :role added for level :level',
        'deleted' => 'All role rewards for :level deleted',
        'number' => 'Both level and role ID need to be numeric.'
    ],

    'xp' => [
        'not-found' => ':user does not have any messages',
        'count' => 'You have :messages',
        'title' => 'Level :level',
        'description' => "User: :user\nLevel: :level \nXP: :xp\nMessages: :messages \nVoice: :voice",
        'given' => ':xp xp given to <@:user>',
        'removed' => ':xp xp removed from <@:user>',
        'reset' => 'xp for <@:user> is reset',
        'not-exist' => 'User <@:user> has no messages or experience in this server'
    ],

    'set' => [
        'title' => 'General bot settings',
        'usage-set' => 'Usage: set `setting_key` `setting_value`',
        'not-exist' => 'Setting :key does not exist',
        'updated' => 'Setting :key is updated to value :value',
        'not-numeric' => 'Setting values must be numeric, :value is not a numeric value',
    ],

    'messages' => [
        'title' => 'xp and level statistics',
        'description' => "List of messages and xp for users\n\n:users",
    ],

    'buttons' => [
        'next' => 'Next Page',
        'previous' => 'Previous Page'
    ],

    'adminstats' => [
        'title' => 'Moderator statistics',
        'description' => "Who got the power?\n\n",
    ],

    'bump' => [
        'inc' => ":name has bumped this discord :count times!",
        'title' => 'Bump Elites',
        'description' => "Bump counters of all time!\n\n:bumpers",
        'description-month' => "Bump counters of this month!\n\n:bumpers",
    ],

    'cringe' => [
        'title' => 'Cringe Counter',
        'description' => "List of the most cringe people in our discord! \n\n:users",
        'count' => "Cringe counter for :name is :count",
        'change' => "Cringe counter for :name is now :count",
        'not-cringe' => ":name is not cringe",
        'reset' => "Cringe for :user is reset to 0",
        'fail' => "Nice try noob, I increased your cringe counter instead. Count is now :count"
    ],

    'cmd' => [
        'saved' => 'Command :trigger saved with response :response',
        'deleted' => 'Command :trigger deleted',
        'title' => 'Commands',
        'description' => "Basic text commands. \n\n :cmds",
    ],

    'reactions' => [
        'saved' => 'Reaction :reaction on :name saved',
        'deleted' => 'Reaction for :name deleted',
        'footer' => 'usage: addreaction, delreaction, reactions',
        'title' => 'Reactions',
        'description' => "Basic reactions.",
    ],

    'timeout' => [
        'title' => 'Timeouts',
        'count' => "Total timeouts: :count",
    ],

    'emotes' => [
        'title' => 'Emote Counter',
        'description' => "List of most used emotes\n\n:emotes",
    ],

    'help' => [
        'title' => 'Help',
        'footer' => 'Switch between sections below',
    ],

    'mention' => [
        'title' => 'Mention Responses',
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
        'updated' => 'Mention group updated',
    ],
    'log' => [
        'no-dm' => 'Slash commands dont work in DM',
        'failed' => 'Failed to use :trigger, lacks permission.',
        'success' => 'Used :trigger.',
        'joined' => '<@:user> joined the server',
        'kicked' => '<@:user> kicked from the server',
        'banned' => '<@:user> banned from the server',
        'left' => '<@:user> left the server',
        'unbanned' => '<@:user> was unbanned from the server',
        'username-change' => "**Username changed** \n\n **From**\n:from\n\n**To**\n:to\n",
        'create-invite' => '<@:inviter> created a new invite link',
        'remove-invite' => 'Invite link by <@:inviter> removed',
        'send-dm' => "Send DM:\n\n :content",
        'update-msg' => "<@:user> updated message in <#:channel>\n\n**Old Message**\n:old\n\n**New Message**\n:new",
        'delete-msg' => "<@:user> deleted his message in <#:channel>\n\n**Message**\n:message",
        'timeout' => '<@:user> has received a timeout',
        'joined-call' => "<@:user> joined <#:channel>",
        'left-call' => "<@:user> left <#:channel>",
        'switch-call' => '<@:user> switched from <#:oldchannel> to <#:newchannel>',
        'muted-call' => '<@:user> was muted in voice',
        'unmuted-call' => '<@:user> was unmuted in voice',
        'start-stream' => '<@:user> started streaming in <#:channel>',
        'stop-stream' => '<@:user> stopped streaming in <#:channel>',
        'enable-cam' => '<@:user> enabled his webcam in <#:channel>',
        'disable-cam' => '<@:user> disabled his webcam in <#:channel>',
    ],
];
