<?php

namespace App\Discord\Core;

use App\Discord\Events\BumpCounter;
use App\Discord\Events\DetectTimeouts;
use App\Discord\Events\EmoteCounter;
use App\Discord\Events\GuildMemberLogger;
use App\Discord\Events\InviteLogger;
use App\Discord\Events\KickAndBanCounter;
use App\Discord\Events\MediaFilter;
use App\Discord\Events\MessageLogger;
use App\Discord\Events\MessageXpCounter;
use App\Discord\Events\StickerFilter;
use App\Discord\Events\TimeoutLogger;
use App\Discord\Events\VoiceStateLogger;
use App\Discord\Events\VoiceStateUpdate;
use App\Discord\Events\VoiceXpCounter;
use App\Discord\Fun\Ask;
use App\Discord\Fun\BumpStatistics;
use App\Discord\Fun\Cringe\CringeIndex;
use App\Discord\Fun\Cringe\DecreaseCringe;
use App\Discord\Fun\Cringe\IncreaseCringe;
use App\Discord\Fun\Cringe\ResetCringe;
use App\Discord\Fun\EightBall;
use App\Discord\Fun\EmoteIndex;
use App\Discord\Fun\MentionResponder\AddMentionGroup;
use App\Discord\Fun\MentionResponder\AddMentionReply;
use App\Discord\Fun\MentionResponder\DelMentionGroup;
use App\Discord\Fun\MentionResponder\DelMentionReply;
use App\Discord\Fun\MentionResponder\MentionGroupIndex;
use App\Discord\Fun\MentionResponder\MentionIndex;
use App\Discord\Fun\MentionResponder\UpdateMentionGroup;
use App\Discord\Fun\Reaction\CreateReaction;
use App\Discord\Fun\Reaction\DeleteReaction;
use App\Discord\Fun\Reaction\ReactionIndex;
use App\Discord\Fun\UrbanDictionary;
use App\Discord\Help;
use App\Discord\JobTest;
use App\Discord\Levels\CreateRoleReward;
use App\Discord\Levels\DeleteRoleReward;
use App\Discord\Levels\GiveXp;
use App\Discord\Levels\Leaderboard;
use App\Discord\Levels\RemoveXp;
use App\Discord\Levels\ResetXp;
use App\Discord\Levels\RoleRewards;
use App\Discord\Levels\UserRank;
use App\Discord\Logger\LogSettings;
use App\Discord\Logger\UpdateLogSetting;
use App\Discord\Moderation\Channels\ChannelIndex;
use App\Discord\Moderation\Channels\MarkChannel;
use App\Discord\Moderation\Channels\UnmarkChannel;
use App\Discord\Moderation\Command\CommandIndex;
use App\Discord\Moderation\Command\CreateCommand;
use App\Discord\Moderation\Command\DeleteCommand;
use App\Discord\Moderation\ModeratorStatistics;
use App\Discord\Moderation\Timeout\Timeouts;
use App\Discord\OpenAi\GenerateImage;
use App\Discord\Roles\AttachRolePermission;
use App\Discord\Roles\AttachUserRole;
use App\Discord\Roles\CreateRole;
use App\Discord\Roles\DeleteRole;
use App\Discord\Roles\DetachRolePermission;
use App\Discord\Roles\DetachUserRole;
use App\Discord\Roles\Permissions;
use App\Discord\Roles\Roles;
use App\Discord\Roles\UserRoles;
use App\Discord\Roles\Users;
use App\Discord\Settings\Settings;
use App\Discord\Settings\UpdateSetting;
use App\Models\Guild;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\WebSockets\Intents;
use Exception;

/**
 * Main bot class, theoretically you could create more instances from this class to have multiple bots running.
 *
 * @property $discord           Set with the global discord instance from DiscordPHP.
 * @property $guilds            List of all active guilds using the bot.
 *
 * @property $devMode           If the bot runs in dev mode.
 * @property $events            DiscordEvent listeners
 * @property $commands          Commands by category
 * @property $devCommands       Commands only in dev mode
 *
 */
class Bot
{
    public Discord $discord;
    private array $guilds;
    private bool $devMode, $updateCommands, $deleteCommands;

    private array $events = [
        VoiceStateUpdate::class,
        DetectTimeouts::class,
        MediaFilter::class,
        StickerFilter::class,
        KickAndBanCounter::class,
        BumpCounter::class,
        EmoteCounter::class,
        MessageXpCounter::class,
        VoiceXpCounter::class,
        VoiceStateLogger::class,
        GuildMemberLogger::class,
        MessageLogger::class,
        TimeoutLogger::class,
        InviteLogger::class,
    ];

    private array $devCommands = [
        JobTest::class,
    ];

    private array $commands = [
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
            Settings::class,
            UpdateSetting::class,
        ],
        'xp' => [
            Leaderboard::class,
            RoleRewards::class,
            CreateRoleReward::class,
            DeleteRoleReward::class,
            UserRank::class,
            GiveXp::class,
            RemoveXp::class,
            ResetXp::class,
        ],
        'mods' => [
            ChannelIndex::class,
            MarkChannel::class,
            UnmarkChannel::class,
            Help::class,
            LogSettings::class,
            UpdateLogSetting::class,
            Timeouts::class,
            ModeratorStatistics::class,
        ],
        'fun' =>
            [
                CringeIndex::class,
                IncreaseCringe::class,
                DecreaseCringe::class,
                ResetCringe::class,
                BumpStatistics::class,
                EmoteIndex::class,
                CommandIndex::class,
                CreateCommand::class,
                DeleteCommand::class,
                ReactionIndex::class,
                CreateReaction::class,
                DeleteReaction::class,
                EightBall::class,
                Ask::class,
                UrbanDictionary::class,
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
    ];


    /**
     * @param bool $devMode
     * @param bool $updateCommands
     * @param $deleteCommands
     */
    public function __construct(bool $devMode = false, bool $updateCommands = false, $deleteCommands = false)
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
        foreach ($this->events as $class) {
            $instance = new $class($this);
            $instance->registerEvent();
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
                $this->loadCommands($commands);
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
