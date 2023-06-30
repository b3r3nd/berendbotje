<?php

namespace App\Discord\Core;

use App\Discord\Blacklist\Commands\Abusers;
use App\Discord\Blacklist\Commands\Block;
use App\Discord\Blacklist\Commands\Unblock;
use App\Discord\Bump\Actions\BumpCounter;
use App\Discord\Bump\Commands\BumpStatistics;
use App\Discord\ChannelFlags\Commands\ChannelIndex;
use App\Discord\ChannelFlags\Commands\MarkChannel;
use App\Discord\ChannelFlags\Commands\UnmarkChannel;
use App\Discord\ChannelFlags\Events\MediaFilter;
use App\Discord\ChannelFlags\Events\StickerFilter;
use App\Discord\Core\Models\Guild;
use App\Discord\Cringe\Commands\CringeIndex;
use App\Discord\Cringe\Commands\DecreaseCringe;
use App\Discord\Cringe\Commands\IncreaseCringe;
use App\Discord\Cringe\Commands\ResetCringe;
use App\Discord\CustomCommands\Commands\CommandIndex;
use App\Discord\CustomCommands\Commands\CreateCommand;
use App\Discord\CustomCommands\Commands\DeleteCommand;
use App\Discord\CustomCommands\Events\CommandResponse;
use App\Discord\Fun\Commands\Ask;
use App\Discord\Fun\Commands\EightBall;
use App\Discord\Fun\Commands\EmoteIndex;
use App\Discord\Fun\Commands\ModeratorStatistics;
use App\Discord\Fun\Commands\UrbanDictionary;
use App\Discord\Fun\Events\Count;
use App\Discord\Fun\Events\EmoteCounter;
use App\Discord\Fun\Events\KickAndBanCounter;
use App\Discord\Fun\Events\Reminder;
use App\Discord\Help\Commands\Help;
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
use App\Discord\OpenAi\Commands\GenerateImage;
use App\Discord\Reaction\Commands\CreateReaction;
use App\Discord\Reaction\Commands\DeleteReaction;
use App\Discord\Reaction\Commands\ReactionIndex;
use App\Discord\Reaction\Events\React;
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
use App\Discord\Settings\Commands\Settings;
use App\Discord\Settings\Commands\UpdateSetting;
use App\Discord\Test\Commands\Test;
use App\Discord\Timeouts\Commands\Timeouts;
use App\Discord\Timeouts\Events\DetectTimeouts;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\WebSockets\Intents;
use Exception;

/**
 * Main bot class, theoretically you could create more instances from this class to have multiple bots running.
 *
 * @property Discord $discord           Set with the global discord instance from DiscordPHP.
 * @property array $guilds            List of all active guilds using the bot.
 *
 * @property bool $devMode           If the bot runs in dev mode.
 * @property bool $updateCommands    If we need to update commands
 * @property bool $deleteCommands    If we need to delete commands
 * @property array $events            DiscordEvent listeners
 * @property array $commands          Commands by category
 * @property array $devCommands       Commands only in dev mode
 *
 */
class Bot
{
    public Discord $discord;
    private array $guilds;
    private bool $devMode, $updateCommands, $deleteCommands;

    private array $events = [
        'levels' => [
            VoiceStateUpdate::class,
            MessageXpCounter::class,
            VoiceXpCounter::class,
        ],
        'timeout' => [
            DetectTimeouts::class,
        ],
        'bump' => [
            BumpCounter::class,
        ],
        'channel-flags' => [
            MediaFilter::class,
            StickerFilter::class,
        ],
        'fun' => [
            KickAndBanCounter::class,
            EmoteCounter::class,
            Reminder::class,
            Count::class,
        ],
        'logger' => [
            VoiceStateLogger::class,
            GuildMemberLogger::class,
            MessageLogger::class,
            TimeoutLogger::class,
            InviteLogger::class,
        ],
        'reactions' => [
            React::class,
        ],
        'custom-commands' => [
            CommandResponse::class,
        ]
    ];

    private array $devCommands = [
        Test::class,
        Help::class,
        Settings::class,
        UpdateSetting::class,
    ];

    private array $commands = [
        'blacklist' => [
            Abusers::class,
            Unblock::class,
            Block::class,
        ],
        'openai' => [
            GenerateImage::class,
        ],
        'roles' => [
            Roles::class,
            Permissions::class,
            Users::class,
            UserRoles::class,
            AttachRolePermission::class,
            DetachRolePermission::class,
            CreateRole::class,
            DeleteRole::class,
            DetachUserRole::class,
            AttachUserRole::class,
        ],
        'settings' => [
            Settings::class,
            UpdateSetting::class,
        ],
        'levels' => [
            Leaderboard::class,
            RoleRewards::class,
            CreateRoleReward::class,
            DeleteRoleReward::class,
            UserRank::class,
            GiveXp::class,
            RemoveXp::class,
            ResetXp::class,
        ],
        'timeout' => [
            Timeouts::class,
        ],
        'reactions' => [
            ReactionIndex::class,
            CreateReaction::class,
            DeleteReaction::class,
        ],
        'mention' => [
            MentionIndex::class,
            AddMentionReply::class,
            DelMentionReply::class,
            MentionGroupIndex::class,
            AddMentionGroup::class,
            DelMentionGroup::class,
            UpdateMentionGroup::class,
        ],
        'bump' => [
            BumpStatistics::class,
        ],
        'channel-flags' => [
            ChannelIndex::class,
            MarkChannel::class,
            UnmarkChannel::class,
        ],
        'cringe' => [
            CringeIndex::class,
            IncreaseCringe::class,
            DecreaseCringe::class,
            ResetCringe::class,
        ],
        'custom-commands' => [
            CommandIndex::class,
            CreateCommand::class,
            DeleteCommand::class,
        ],
        'fun' => [
            EmoteIndex::class,
            EightBall::class,
            Ask::class,
            UrbanDictionary::class,
            ModeratorStatistics::class,
        ],
        'help' => [
            Help::class,
        ],
        'logger' => [
            LogSettings::class,
            UpdateLogSetting::class,
        ],
    ];


    /**
     * @param bool $devMode
     * @param bool $updateCommands
     * @param bool $deleteCommands
     */
    public function __construct(bool $devMode = false, bool $updateCommands = false, bool $deleteCommands = false)
    {
        $this->devMode = $devMode;
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
            if ($this->updateCommands || $this->devMode) {
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
            if (config("discord.modules.{$category}")) {
                foreach ($events as $class) {
                    $instance = new $class($this);
                    $instance->registerEvent();
                }
            }
        }
    }

    /**
     * @param array $commands
     * @return void
     */
    private function loadCommands(array $commands): void
    {
        foreach ($commands as $class) {
            $instance = new $class();
            $instance->setBot($this);
            $instance->registerSlashCommand($this);
        }
    }

    /**
     * @return void
     */
    private function updateSlashCommands(): void
    {
        if ($this->devMode) {
            $this->loadCommands($this->devCommands);
        } else {
            foreach ($this->commands as $category => $commands) {
                if (config("discord.modules.{$category}")) {
                    $this->loadCommands($commands);
                }
            }
        }
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
