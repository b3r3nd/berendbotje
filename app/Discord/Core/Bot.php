<?php

namespace App\Discord\Core;

use App\Discord\Core\Commands\Settings;
use App\Discord\Core\Commands\UpdateSetting;
use App\Discord\Core\Commands\UpdateUserSetting;
use App\Discord\Core\Commands\UserSettings;
use App\Discord\Core\Models\Guild;
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
use App\Discord\Fun\Commands\GenerateImage;
use App\Discord\Fun\Commands\IncreaseCringe;
use App\Discord\Fun\Commands\ModeratorStatistics;
use App\Discord\Fun\Commands\ReactionIndex;
use App\Discord\Fun\Commands\ResetCringe;
use App\Discord\Fun\Commands\UrbanDictionary;
use App\Discord\Fun\Events\BumpCounter;
use App\Discord\Fun\Events\CommandResponse;
use App\Discord\Fun\Events\Count;
use App\Discord\Fun\Events\EmoteCounter;
use App\Discord\Fun\Events\KickAndBanCounter;
use App\Discord\Fun\Events\React;
use App\Discord\Fun\Events\Reminder;
use App\Discord\Help;
use App\Discord\Levels\Commands\CreateRoleReward;
use App\Discord\Levels\Commands\DeleteRoleReward;
use App\Discord\Levels\Commands\GiveXp;
use App\Discord\Levels\Commands\Leaderboard;
use App\Discord\Levels\Commands\RemoveXp;
use App\Discord\Levels\Commands\ResetXp;
use App\Discord\Levels\Commands\RoleRewards;
use App\Discord\Levels\Commands\UserRank;
use App\Discord\Levels\Events\MessageXpCounter;
use App\Discord\Levels\Events\VoiceStateUpdate;
use App\Discord\Levels\Events\VoiceXpCounter;
use App\Discord\Logger\Commands\LogSettings;
use App\Discord\Logger\Commands\UpdateLogSetting;
use App\Discord\Logger\Events\GuildMemberLogger;
use App\Discord\Logger\Events\InviteLogger;
use App\Discord\Logger\Events\MessageLogger;
use App\Discord\Logger\Events\TimeoutLogger;
use App\Discord\Logger\Events\VoiceStateLogger;
use App\Discord\MentionResponder\Commands\AddMentionGroup;
use App\Discord\MentionResponder\Commands\AddMentionReply;
use App\Discord\MentionResponder\Commands\DelMentionGroup;
use App\Discord\MentionResponder\Commands\DelMentionReply;
use App\Discord\MentionResponder\Commands\MentionGroupIndex;
use App\Discord\MentionResponder\Commands\MentionIndex;
use App\Discord\MentionResponder\Commands\UpdateMentionGroup;
use App\Discord\Moderation\Commands\Blacklist;
use App\Discord\Moderation\Commands\Block;
use App\Discord\Moderation\Commands\ChannelIndex;
use App\Discord\Moderation\Commands\RemoveTimeout;
use App\Discord\Moderation\Commands\UpdateTimeoutReason;
use App\Discord\Moderation\Commands\MarkChannel;
use App\Discord\Moderation\Commands\Timeouts;
use App\Discord\Moderation\Commands\Unblock;
use App\Discord\Moderation\Commands\UnmarkChannel;
use App\Discord\Moderation\Events\DetectTimeouts;
use App\Discord\Moderation\Events\GiveJoinRole;
use App\Discord\Moderation\Events\MediaFilter;
use App\Discord\Moderation\Events\StickerFilter;
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
use App\Discord\Test\Commands\Test;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\InteractionType;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Exception;

/**
 * Main bot class, theoretically you could create more instances from this class to have multiple bots running.
 *
 * @property Discord $discord         Set with the global discord instance from DiscordPHP.
 * @property array $guilds            List of all active guilds using the bot.
 * @property bool $updateCommands     If we need to update commands.
 * @property bool $deleteCommands     If we need to delete commands.
 * @property array $events            DiscordEvent listeners.
 * @property array $commands          Commands by category.
 */
class Bot
{
    public Discord $discord;
    private array $guilds;
    private bool $updateCommands, $deleteCommands;

    private array $events = [
        'levels' => [
            VoiceStateUpdate::class,
            MessageXpCounter::class,
            VoiceXpCounter::class,
        ],
        'moderation' => [
            DetectTimeouts::class,
            MediaFilter::class,
            StickerFilter::class,
            GiveJoinRole::class,
        ],
        'fun' => [
            BumpCounter::class,
            KickAndBanCounter::class,
            EmoteCounter::class,
            Reminder::class,
            Count::class,
            React::class,
            CommandResponse::class,
        ],
        'logger' => [
            VoiceStateLogger::class,
            GuildMemberLogger::class,
            MessageLogger::class,
            TimeoutLogger::class,
            InviteLogger::class,
        ],
    ];

    private array $commands = [];

    // list, add, delete, edit !!!!!!!!
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

        'mentionreplies' => [
            MentionIndex::class,
            AddMentionReply::class,
            DelMentionReply::class,
        ],
        'mentiongroups' => [
            MentionGroupIndex::class,
            AddMentionGroup::class,
            DelMentionGroup::class,
            UpdateMentionGroup::class,
        ],
        'fun' => [
            GenerateImage::class,
            BumpStatistics::class,
            EmoteIndex::class,
            EightBall::class,
            Ask::class,
            UrbanDictionary::class,
            ModeratorStatistics::class,
        ],
        'help' => [
            Help::class,
        ],
        'test' => [
            Test::class,
        ],
    ];


    /**
     * @param bool $updateCommands
     * @param bool $deleteCommands
     */
    public function __construct(bool $updateCommands = false, bool $deleteCommands = false)
    {
        $this->updateCommands = $updateCommands;
        $this->deleteCommands = $deleteCommands;
    }

    /**
     * @return void
     * @throws IntentException
     */
    public function connect(): void
    {
        $this->discord = new Discord([
                'token' => config('discord.token'),
                'loadAllMembers' => true,
                'storeMessages' => true,
                'intents' => Intents::getDefaultIntents() | Intents::GUILD_VOICE_STATES | Intents::GUILD_MEMBERS |
                    Intents::MESSAGE_CONTENT | Intents::GUILDS | Intents::GUILD_INVITES | Intents::GUILD_EMOJIS_AND_STICKERS
            ]
        );
        $this->discord->on('ready', function (Discord $discord) {
            $this->loadEvents();
            $this->loadGuilds();
            if ($this->deleteCommands) {
                $this->deleteSlashCommands();
            }
            if ($this->updateCommands) {
                $this->updateSlashCommands();
            }
        });
        $this->discord->run();
    }

    /**
     * @return void
     */
    private function loadEvents(): void
    {
        foreach ($this->events as $category => $events) {
            foreach ($events as $class) {
                $instance = new $class($this);
                $instance->registerEvent();
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function updateSlashCommands(): void
    {
        foreach ($this->slashCommandStructure as $mainCommand => $subGroups) {
            $subGroupOptions = [];
            foreach ($subGroups as $subGroup => $subCommands) {
                if (is_array($subCommands)) {
                    $subCommandOptions = [];
                    foreach ($subCommands as $subCommand) {
                        $subCommandOptions[] = $this->getOptions($subCommand, $subGroup);
                    }
                    $subGroupOptions[] = [
                        'name' => $subGroup,
                        'description' => $subGroup,
                        'type' => 2,
                        'options' => $subCommandOptions,
                    ];
                } else {
                    $subGroupOptions[] = $this->getOptions($subCommands, $mainCommand);
                }
            }
            $optionsArray = [
                'name' => $mainCommand,
                'description' => $mainCommand,
                'options' => $subGroupOptions,
            ];

            $command = new \Discord\Parts\Interactions\Command\Command($this->discord, $optionsArray);
            $this->discord->application->commands->save($command);
        }

        $this->discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
            $trigger = "{$interaction->data->name}_{$interaction->data->options->first()?->name}";
            if (!isset($this->commands[$trigger]) && $interaction->data->options->first()?->options->first()) {
                $trigger = "{$interaction->data->options->first()?->name}_{$interaction->data->options->first()->options->first()?->name}";
            }
            if (isset($this->commands[$trigger])) {
                if ($interaction->type === InteractionType::APPLICATION_COMMAND) {
                    $this->commands[$trigger]->execute($interaction);
                }
                if ($interaction->type === InteractionType::APPLICATION_COMMAND_AUTOCOMPLETE) {
                    $this->commands[$trigger]->complete($interaction);
                }
            }
        });
    }

    /**
     * @param $command
     * @param $subGroup
     * @return array
     */
    private function getOptions($command, $subGroup): array
    {
        $instance = new $command();
        $instance->setBot($this);
        $this->commands["{$subGroup}_{$instance->trigger}"] = $instance;
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
        $this->discord->application->commands->freshen()->done(function ($commands) {
            foreach ($commands as $command) {
                $this->discord->application->commands->delete($command);
            }
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function loadGuilds(): void
    {
        foreach (Guild::all() as $guild) {
            $this->guilds[$guild->guild_id] = new \App\Discord\Core\Guild($guild, $this);
        }
    }

    /**
     * @param string $id
     * @return mixed|null
     */
    public function getGuild(string $id): mixed
    {
        return $this->guilds[$id] ?? null;
    }

    /**
     * @return array
     */
    public function getGuilds(): array
    {
        return $this->guilds;
    }

}
