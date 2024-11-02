<?php

use App\Discord\Fun\Commands\BumpStatistics;
use App\Discord\Fun\Commands\Count\CountStats;
use App\Discord\Fun\Commands\Count\CountUserStats;
use App\Discord\Fun\Commands\Ask;
use App\Discord\Fun\Commands\Cringe\CringeIndex;
use App\Discord\Fun\Commands\Cringe\DecreaseCringe;
use App\Discord\Fun\Commands\Cringe\IncreaseCringe;
use App\Discord\Fun\Commands\Cringe\ResetCringe;
use App\Discord\Fun\Commands\EightBall;
use App\Discord\Fun\Commands\ModeratorStatistics;
use App\Discord\Fun\Commands\UrbanDictionary;
use App\Discord\Help\Commands\Support;
use App\Discord\Help\Commands\Help;
use App\Discord\Help\Commands\Vote;
use App\Discord\Levels\Commands\DurationReward\CreateDurationReward;
use App\Discord\Levels\Commands\DurationReward\DeleteDurationReward;
use App\Discord\Levels\Commands\DurationReward\DurationRewards;
use App\Discord\Levels\Commands\DurationReward\SyncDurationRewards;
use App\Discord\Levels\Commands\GiveXp;
use App\Discord\Levels\Commands\Leaderboard;
use App\Discord\Levels\Commands\LevelReward\CreateRoleReward;
use App\Discord\Levels\Commands\LevelReward\DeleteRoleReward;
use App\Discord\Levels\Commands\LevelReward\RoleRewards;
use App\Discord\Levels\Commands\RemoveXp;
use App\Discord\Levels\Commands\ResetXp;
use App\Discord\Levels\Commands\UserRank;
use App\Discord\Logger\Commands\LogSettings;
use App\Discord\Logger\Commands\UpdateLogSetting;
use App\Discord\Message\Commands\Level\AddLevelMessage;
use App\Discord\Message\Commands\Level\DeleteLevelMessage;
use App\Discord\Message\Commands\Level\LevelMessagesIndex;
use App\Discord\Message\Commands\Mention\AddMentionGroup;
use App\Discord\Message\Commands\Mention\AddMentionReply;
use App\Discord\Message\Commands\Mention\DelMentionGroup;
use App\Discord\Message\Commands\Mention\DelMentionReply;
use App\Discord\Message\Commands\Mention\MentionGroupIndex;
use App\Discord\Message\Commands\Mention\MentionIndex;
use App\Discord\Message\Commands\Mention\UpdateMentionGroup;
use App\Discord\Message\Commands\Welcome\AddWelcomeMessage;
use App\Discord\Message\Commands\Welcome\DeleteWelcomeMessage;
use App\Discord\Message\Commands\Welcome\WelcomeMessagesIndex;
use App\Discord\Moderation\Commands\Blacklist\Block;
use App\Discord\Moderation\Commands\Blacklist\Unblock;
use App\Discord\Moderation\Commands\ToggleInvites;
use App\Discord\Moderation\Commands\Blacklist\Blacklist;
use App\Discord\Moderation\Commands\Reminder\CreateReminder;
use App\Discord\Moderation\Commands\Reminder\DeleteReminder;
use App\Discord\Moderation\Commands\Reminder\Reminders;
use App\Discord\Moderation\Commands\Timeout\RemoveTimeout;
use App\Discord\Moderation\Commands\Timeout\Timeouts;
use App\Discord\Moderation\Commands\Timeout\UpdateTimeoutReason;
use App\Discord\Roles\Commands\AttachRolePermission;
use App\Discord\Roles\Commands\AttachUserRole;
use App\Discord\Roles\Commands\CreateRole;
use App\Discord\Roles\Commands\DeleteRole;
use App\Discord\Roles\Commands\DetachRolePermission;
use App\Discord\Roles\Commands\DetachUserRole;
use App\Discord\Roles\Commands\Permissions;
use App\Discord\Roles\Commands\Roles;
use App\Discord\Roles\Commands\UserRoles;
use App\Discord\Roles\Commands\Users;
use App\Discord\Setting\Commands\Settings;
use App\Discord\Setting\Commands\UpdateSetting;
use App\Discord\Setting\Commands\UpdateUserSetting;
use App\Discord\Setting\Commands\UserSettings;
use  App\Discord\Fun\Commands\Count\Leaderboard as CountLeaderboard;

return [
    /*
    |--------------------------------------------------------------------------
    | Slash Command Structure
    |--------------------------------------------------------------------------
    |
    | Here you may specify all your slash commands used by the bot. Remember to
    | add classes which extend the SlashCommand or SlashIndexCommand.
    |
    | 'GLOBAL' AND 'GUILD' ARE NOT PART OF THE STRUCTURE!!!!
    |
    |--------------------------------------------------------------------------
    |   command
    |   |
    |   |__ subcommand
    |   |__ subcommand
    |
    |--------------------------------------------------------------------------
    |   command
    |   |
    |   |__ subcommand-group
    |       |
    |       |__ subcommand
    |   |
    |   |__ subcommand-group
    |       |
    |       |__ subcommand
    |       |__ subcommand
    |
    |--------------------------------------------------------------------------
    |   command
    |   |
    |   |__ subcommand-group
    |       |
    |       |__ subcommand
    |   |
    |   |__ subcommand
    |
    */

    'global' => [
        'users' => [
            Users::class,
            UserRoles::class,
            DetachUserRole::class,
            AttachUserRole::class,
        ],
        'roles' => [
            Roles::class,
            CreateRole::class,
            DeleteRole::class,
        ],
        'permissions' => [
            Permissions::class,
            AttachRolePermission::class,
            DetachRolePermission::class,
        ],
        'config' => [
            'guild' => [
                Settings::class,
                UpdateSetting::class,
            ],
            'user' => [
                UserSettings::class,
                UpdateUserSetting::class
            ],
            'log' => [
                LogSettings::class,
                UpdateLogSetting::class,
            ],
        ],
        'timeouts' => [
            Timeouts::class,
            UpdateTimeoutReason::class,
            RemoveTimeout::class,
        ],
        // 'blacklist' => [
        //     Blacklist::class,
        //     Unblock::class,
        //     Block::class,
        // ],
        'rewards' => [
            'level' => [
                RoleRewards::class,
                CreateRoleReward::class,
                DeleteRoleReward::class,
            ],
            'duration' => [
                DurationRewards::class,
                CreateDurationReward::class,
                DeleteDurationReward::class,
                SyncDurationRewards::class,
            ]
        ],
         'xp' => [
        //     GiveXp::class,
        //     RemoveXp::class,
        //     ResetXp::class,
             Leaderboard::class,
             UserRank::class,
         ],
        'mention' => [
            'replies' => [
                MentionIndex::class,
                AddMentionReply::class,
                DelMentionReply::class,
            ],
            'groups' => [
                MentionGroupIndex::class,
                AddMentionGroup::class,
                DelMentionGroup::class,
                UpdateMentionGroup::class,
            ],
        ],
        // 'cringe' => [
        //     CringeIndex::class,
        //     IncreaseCringe::class,
        //     DecreaseCringe::class,
        //     ResetCringe::class,
        // ],
         'fun' => [
        //     'count' => [
        //         CountStats::class,
        //         CountLeaderboard::class,
        //         CountUserStats::class
        //     ],
             BumpStatistics::class,
             EightBall::class,
             Ask::class,
             UrbanDictionary::class,
        //     ModeratorStatistics::class,
        //     //      EmoteIndex::class,
         ],
        'invites' => [
            ToggleInvites::class,
        ],
        'messages' => [
            'welcome' => [
                WelcomeMessagesIndex::class,
                AddWelcomeMessage::class,
                DeleteWelcomeMessage::class,
            ],
            'levels' => [
                LevelMessagesIndex::class,
                AddLevelMessage::class,
                DeleteLevelMessage::class,
            ]
        ],
        'reminders' => [
            CreateReminder::class,
            DeleteReminder::class,
            Reminders::class,
        ],
        'help' => [
            Help::class,
       //     Vote::class,
            Support::class
        ],
//        'reactions' => [
//            ReactionIndex::class,
//            CreateReaction::class,
//            DeleteReaction::class,
//        ],
//        'commands' => [
//            CommandIndex::class,
//            CreateCommand::class,
//            DeleteCommand::class,
//        ],
//        'channels' => [
//            ChannelIndex::class,
//            MarkChannel::class,
//            UnmarkChannel::class,
//        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Guild Commands
    |--------------------------------------------------------------------------
    |
    | Commands for the support guild only (set in the env).
    |
    | 'GLOBAL' AND 'GUILD' ARE NOT PART OF THE STRUCTURE!!!!
    |
    */
    'guild' => [
//        'guilds' => [
//            DatabaseGuilds::class,
//        ],
//        'topgg' => [
//            UpdateTopGG::class
//        ],
    ],
];
