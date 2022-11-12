<?php

namespace App\Discord\Core;

use App\Discord\Core\DiscordEvents\VoiceStateUpdate;
use App\Discord\Fun\Bump\BumpCounter;
use App\Discord\Fun\Emote\EmoteCounter;
use App\Discord\Fun\MentionResponder;
use App\Discord\Fun\Reaction\SimpleReaction;
use App\Discord\Help;
use App\Discord\Levels\MessageXpCounter;
use App\Discord\Levels\VoiceXpCounter;
use App\Discord\Logger\Events\GuildMemberLogger;
use App\Discord\Logger\Events\InviteLogger;
use App\Discord\Logger\Events\MessageLogger;
use App\Discord\Logger\Events\TimeoutLogger;
use App\Discord\Logger\Events\VoiceStateLogger;
use App\Discord\Logger\LogSettings;
use App\Discord\Logger\UpdateLogSetting;
use App\Discord\Moderation\Channels\MediaFilter;
use App\Discord\Moderation\Command\SimpleCommand;
use App\Discord\Moderation\KickAndBanCounter;
use App\Discord\Moderation\Timeout\DetectTimeouts;
use App\Discord\Settings\Settings;
use App\Discord\Settings\UpdateSetting;
use App\Models\Guild;
use App\Models\Reaction;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\User\Activity;
use Discord\WebSockets\Intents;
use Exception;

/**
 * We only ever have one instance of this class, you could call it a singleton however it isn't really. This class
 * will always be instantiated when the bot boots. We do not need to check whether that is the case
 * on static function calls. If the instance is not there, everything is broken already.
 *
 * I could pass this as an argument to literally every class constructor (which I did at first) but it became
 * cumbersome rather quick, hence this implementation.
 *
 * @property $discord           Set with the global discord instance from DiscordPHP.
 * @property $instance          Static instance of self (singleton) accessible through static call get().
 * @property $guilds            List of all active guilds using the bot.
 */
class Bot
{
    private Discord $discord;
    private array $guilds;
    private static self $instance;


    /**
     * Define all events that do not require commands to be triggered, for example the media filter or voice states.
     * @return string[]
     */
    private function coreClasses(): array
    {
        return [
            VoiceStateUpdate::class,
            DetectTimeouts::class,
            MediaFilter::class,
            MentionResponder::class,

            KickAndBanCounter::class,
            BumpCounter::class,
            EmoteCounter::class,
            MessageXpCounter::class,
            VoiceXpCounter::class,

            VoiceStateLogger::class, GuildMemberLogger::class, MessageLogger::class, TimeoutLogger::class,
            InviteLogger::class,
        ];
    }

    /**
     * Define all command classes, command classes are implementations of either of the 4 abstract classes below.
     * @return string[]
     * @see SlashCommand
     * @see SlashAndMessageCommand
     * @see SlashIndexCommand
     *
     * @see MessageCommand
     */
    private function commands(): array
    {
        return [
//            Servers::class,
//
//            Roles::class, Permissions::class, Users::class,
//            MyRoles::class, UserRoles::class,
//            CreateRole::class, DeleteRole::class,
//            AttachRolePermission::class, AttachUserRole::class, DetachRolePermission::class, DetachUserRole::class,
            Settings::class, UpdateSetting::class,
//
//            SingleUserTimeouts::class, AllTimeouts::class, ModeratorStatistics::class,
//
//            Leaderboard::class, RoleRewards::class, CreateRoleReward::class, DeleteRoleReward::class,
//            UserRank::class, GiveXp::class, RemoveXp::class, ResetXp::class,
//
//            CringeIndex::class, IncreaseCringe::class, DecreaseCringe::class, ResetCringe::class,
//            BumpStatistics::class, EmoteIndex::class,
//            CommandIndex::class, CreateCommand::class, DeleteCommand::class,
//            ReactionIndex::class, CreateReaction::class, DeleteReaction::class,
//            EightBall::class, Ask::class, UrbanDictionary::class,
//
//            ChannelIndex::class, MarkChannel::class, UnmarkChannel::class,
//
            Help::class,

            LogSettings::class, UpdateLogSetting::class,

        ];
    }

    /**
     * @throws IntentException
     */
    public function __construct()
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
            $activity = new Activity($this->discord, [
                'type' => Activity::TYPE_WATCHING,
                'name' => __('bot.status'),
            ]);
            $discord->updatePresence($activity);

            $this->loadCoreClasses();
            $this->loadGuilds();
            // $this->deleteSlashCommands();
            $this->loadCommands();
        });
        self::$instance = $this;
        return $this;
    }


    /**
     * @return void
     */
    public function loadGuilds(): void
    {
        foreach (Guild::all() as $guild) {
            $this->guilds[$guild->guild_id] = new \App\Discord\Core\Guild($guild);
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

    /**
     * @return void
     */
    private function loadCoreClasses(): void
    {
        foreach ($this->coreClasses() as $class) {
            new $class();
        }
    }

    /**
     * @return void
     */
    private function loadCommands(): void
    {
        foreach ($this->commands() as $class) {
            $instance = new $class();
            $instance->registerSlashCommand();
        }

        // Custom commands
        foreach (\App\Models\Command::all() as $command) {
            SimpleCommand::create($this, $command->trigger, $command->response, $command->guild->guild_id);
        }

        // Custom reactions
        foreach (Reaction::all() as $reaction) {
            SimpleReaction::create($this, $reaction->trigger, $reaction->reaction, $reaction->guild->guild_id);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    function deleteSlashCommands(): void
    {
        $this->discord->application->commands->freshen()->done(function ($commands) {
            foreach ($commands as $command) {
                $this->discord->application->commands->delete($command);
            }
        });
    }

    /**
     * @return Bot|static
     */
    public static function get(): Bot|static
    {
        return self::$instance;
    }

    /**
     * @return Discord
     */
    public static function getDiscord(): Discord
    {
        return self::$instance->discord;
    }

    /**
     * @return Discord
     */
    public function discord(): Discord
    {
        return $this->discord;
    }

}
