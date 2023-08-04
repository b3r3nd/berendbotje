<?php

namespace App\Discord\Core\Providers;

use App\Discord\Core\Bot;
use App\Discord\Core\Commands\Settings;
use App\Discord\Core\Commands\UpdateSetting;
use App\Discord\Core\Commands\UpdateUserSetting;
use App\Discord\Core\Commands\UserSettings;
use App\Discord\Core\Interfaces\ServiceProvider;
use App\Discord\Core\SlashCommand;
use App\Discord\Fun\Commands\Ask;
use App\Discord\Fun\Commands\BumpStatistics;
use App\Discord\Fun\Commands\CommandIndex;
use App\Discord\Fun\Commands\CreateCommand;
use App\Discord\Fun\Commands\CreateReaction;
use App\Discord\Fun\Commands\CringeIndex;
use App\Discord\Fun\Commands\DecreaseCringe;
use App\Discord\Fun\Commands\DeleteCommand;
use App\Discord\Fun\Commands\DeleteReaction;
use App\Discord\Fun\Commands\EightBall;
use App\Discord\Fun\Commands\EmoteIndex;
use App\Discord\Fun\Commands\IncreaseCringe;
use App\Discord\Fun\Commands\ModeratorStatistics;
use App\Discord\Fun\Commands\ReactionIndex;
use App\Discord\Fun\Commands\ResetCringe;
use App\Discord\Fun\Commands\UrbanDictionary;
use App\Discord\Help;
use App\Discord\Levels\Commands\CreateRoleReward;
use App\Discord\Levels\Commands\DeleteRoleReward;
use App\Discord\Levels\Commands\GiveXp;
use App\Discord\Levels\Commands\Leaderboard;
use App\Discord\Levels\Commands\RemoveXp;
use App\Discord\Levels\Commands\ResetXp;
use App\Discord\Levels\Commands\RoleRewards;
use App\Discord\Levels\Commands\UserRank;
use App\Discord\Logger\Commands\LogSettings;
use App\Discord\Logger\Commands\UpdateLogSetting;
use App\Discord\MentionResponder\Commands\AddMentionGroup;
use App\Discord\MentionResponder\Commands\AddMentionReply;
use App\Discord\MentionResponder\Commands\DelMentionGroup;
use App\Discord\MentionResponder\Commands\DelMentionReply;
use App\Discord\MentionResponder\Commands\MentionGroupIndex;
use App\Discord\MentionResponder\Commands\MentionIndex;
use App\Discord\MentionResponder\Commands\UpdateMentionGroup;
use App\Discord\Moderation\Commands\AddLevelMessage;
use App\Discord\Moderation\Commands\AddWelcomeMessage;
use App\Discord\Moderation\Commands\Blacklist;
use App\Discord\Moderation\Commands\Block;
use App\Discord\Moderation\Commands\ChannelIndex;
use App\Discord\Moderation\Commands\DeleteLevelMessage;
use App\Discord\Moderation\Commands\DeleteWelcomeMessage;
use App\Discord\Moderation\Commands\LevelMessagesIndex;
use App\Discord\Moderation\Commands\MarkChannel;
use App\Discord\Moderation\Commands\RemoveTimeout;
use App\Discord\Moderation\Commands\Timeouts;
use App\Discord\Moderation\Commands\ToggleInvites;
use App\Discord\Moderation\Commands\Unblock;
use App\Discord\Moderation\Commands\UnmarkChannel;
use App\Discord\Moderation\Commands\UpdateTimeoutReason;
use App\Discord\Moderation\Commands\WelcomeMessagesIndex;
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
use Discord\Parts\Interactions\Command\Command;
use Exception;

class SlashCommandServiceProvider implements ServiceProvider
{
    private Bot $bot;
    /**
     * @see https://discord.com/developers/docs/interactions/application-commands#subcommands-and-subcommand-groups
     * @var array
     */
    private array $slashCommandStructure = [
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
        'channels' => [
            ChannelIndex::class,
            MarkChannel::class,
            UnmarkChannel::class,
        ],
        'timeouts' => [
            Timeouts::class,
            UpdateTimeoutReason::class,
            RemoveTimeout::class,
        ],
        'blacklist' => [
            Blacklist::class,
            Unblock::class,
            Block::class,
        ],
        'rolerewards' => [
            RoleRewards::class,
            CreateRoleReward::class,
            DeleteRoleReward::class,
        ],
        'xp' => [
            GiveXp::class,
            RemoveXp::class,
            ResetXp::class,
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
        'cringe' => [
            CringeIndex::class,
            IncreaseCringe::class,
            DecreaseCringe::class,
            ResetCringe::class,
        ],
        'reactions' => [
            ReactionIndex::class,
            CreateReaction::class,
            DeleteReaction::class,
        ],
        'commands' => [
            CommandIndex::class,
            CreateCommand::class,
            DeleteCommand::class,
        ],

        'fun' => [
            BumpStatistics::class,
            EmoteIndex::class,
            EightBall::class,
            Ask::class,
            UrbanDictionary::class,
            ModeratorStatistics::class,
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
        'help' => [
            Help::class,
        ],
    ];


    public function boot(Bot $bot): void
    {
        // Silence is golden..
    }

    /**
     * @throws Exception
     */
    public function init(Bot $bot): void
    {
        $this->bot = $bot;
        if ($bot->needCommandDeletion()) {
            $this->deleteSlashCommands();
        }
        if ($bot->needsCommandUpdate()) {
            $this->updateSlashCommands();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function updateSlashCommands(): void
    {
        foreach ($this->slashCommandStructure as $mainCommand => $subGroups) {
            $subGroupOptions = [];
            foreach ($subGroups as $subGroup => $subCommands) {
                if (is_array($subCommands)) {
                    $subCommandOptions = [];
                    foreach ($subCommands as $subCommand) {
                        $subCommandOptions[] = $this->initCommandOptions($mainCommand, $subCommand, $subGroup);
                    }
                    $subGroupOptions[] = [
                        'name' => $subGroup,
                        'description' => $subGroup,
                        'type' => 2,
                        'options' => $subCommandOptions,
                    ];
                } else {
                    $subGroupOptions[] = $this->initCommandOptions($mainCommand, $subCommands, $mainCommand);
                }
            }
            $optionsArray = [
                'name' => $mainCommand,
                'description' => $mainCommand,
                'options' => $subGroupOptions,
            ];

            $command = new Command($this->bot->discord, $optionsArray);
            $this->bot->discord->application->commands->save($command);
        }
    }

    /**
     * @param $mainCommand
     * @param $command
     * @param $subGroup
     * @return array
     */
    private function initCommandOptions($mainCommand, $command, $subGroup): array
    {
        /** @var SlashCommand $instance */
        $instance = new $command();
        $instance->setBot($this->bot);
        $commandLabel = "{$subGroup}_{$instance->trigger}";
        if ($mainCommand === $subGroup) {
            $instance->setCommandLabel($commandLabel);
        } else {
            $instance->setCommandLabel("{$mainCommand}_{$commandLabel}");
        }
        $this->bot->addSlashCommand($instance, $commandLabel);
        $options = [
            'name' => $instance->trigger,
            'description' => $instance->description,
            'type' => 1,
        ];
        if (isset($instance->slashCommandOptions)) {
            $options['options'] = $instance->slashCommandOptions;
        }
        return $options;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function deleteSlashCommands(): void
    {
        $this->bot->discord->application->commands->freshen()->done(function ($commands) {
            foreach ($commands as $command) {
                $this->bot->discord->application->commands->delete($command);
            }
        });
    }
}
