<?php

namespace App\Discord\Core;

use App\Discord\Administration\Servers;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\Command\SlashCommand;
use App\Discord\Fun\Ask;
use App\Discord\Fun\Bump\BumpCounter;
use App\Discord\Fun\Bump\BumpStatistics;
use App\Discord\Fun\Cringe\CringeIndex;
use App\Discord\Fun\Cringe\DecreaseCringe;
use App\Discord\Fun\Cringe\IncreaseCringe;
use App\Discord\Fun\Cringe\ResetCringe;
use App\Discord\Fun\EightBall;
use App\Discord\Fun\Emote\EmoteCounter;
use App\Discord\Fun\Emote\EmoteIndex;
use App\Discord\Fun\MentionResponder;
use App\Discord\Fun\Message\CreateRoleReward;
use App\Discord\Fun\Message\DeleteRoleReward;
use App\Discord\Fun\Message\MessageCounter;
use App\Discord\Fun\Message\MessagesIndex;
use App\Discord\Fun\Message\RoleRewards;
use App\Discord\Fun\Message\UserMessages;
use App\Discord\Fun\Reaction\CreateReaction;
use App\Discord\Fun\Reaction\DeleteReaction;
use App\Discord\Fun\Reaction\ReactionIndex;
use App\Discord\Fun\Reaction\SimpleReaction;
use App\Discord\Fun\Say;
use App\Discord\Fun\UrbanDictionary;
use App\Discord\Help;
use App\Discord\Moderation\Command\CommandIndex;
use App\Discord\Moderation\Command\CreateCommand;
use App\Discord\Moderation\Command\DeleteCommand;
use App\Discord\Moderation\Command\SimpleCommand;
use App\Discord\Moderation\DetectKicksAndBans;
use App\Discord\Moderation\MediaFilter\CreateMediaChannel;
use App\Discord\Moderation\MediaFilter\DeleteMediaChannel;
use App\Discord\Moderation\MediaFilter\MediaChannels;
use App\Discord\Moderation\MediaFilter\MediaFilter;
use App\Discord\Moderation\ModeratorStatistics;
use App\Discord\Moderation\Timeout\AllTimeouts;
use App\Discord\Moderation\Timeout\DetectTimeouts;
use App\Discord\Moderation\Timeout\SingleUserTimeouts;
use App\Discord\Roles\AttachRolePermission;
use App\Discord\Roles\AttachUserRole;
use App\Discord\Roles\CreateRole;
use App\Discord\Roles\DeleteRole;
use App\Discord\Roles\DetachRolePermission;
use App\Discord\Roles\DetachUserRole;
use App\Discord\Roles\MyRoles;
use App\Discord\Roles\Permissions;
use App\Discord\Roles\Roles;
use App\Discord\Roles\UserRoles;
use App\Discord\Roles\Users;
use App\Discord\Settings\Settings;
use App\Discord\Settings\UpdateSetting;
use App\Discord\TestCommand;
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
 * @property $prefix            Prefix of the bot, can be changed on the fly whenever you like.
 * @property $instance          Static instance of self (singleton) accessible through static call get().
 * @property $guilds            List of all active guilds using the bot.
 */
class Bot
{
    private Discord $discord;
    private array $guilds;
    private string $prefix = '$';
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
            DetectKicksAndBans::class,
            BumpCounter::class,
            EmoteCounter::class,
            MessageCounter::class,
            MentionResponder::class,
        ];
    }

    /**
     * Define all command classes, command classes are implementations of either of the 4 abstract classes below.
     * @return string[]
     * @see SlashCommand
     * @see SlashAndMessageCommand
     * @see SlashAndMessageIndexCommand
     *
     * @see MessageCommand
     */
    private function commands(): array
    {
        return [
            Servers::class,

            Roles::class, Permissions::class, Users::class,
            MyRoles::class, UserRoles::class,
            CreateRole::class, DeleteRole::class,
            AttachRolePermission::class, AttachUserRole::class, DetachRolePermission::class, DetachUserRole::class,
            Settings::class, UpdateSetting::class,

            SingleUserTimeouts::class, AllTimeouts::class, ModeratorStatistics::class,
            CreateMediaChannel::class, DeleteMediaChannel::class, MediaChannels::class,

            MessagesIndex::class, UserMessages::class, RoleRewards::class, CreateRoleReward::class, DeleteRoleReward::class,

            CringeIndex::class, IncreaseCringe::class, DecreaseCringe::class, ResetCringe::class,
            BumpStatistics::class, EmoteIndex::class,
            CommandIndex::class, CreateCommand::class, DeleteCommand::class,
            ReactionIndex::class, CreateReaction::class, DeleteReaction::class,
            EightBall::class, Ask::class, Say::class, UrbanDictionary::class,


            Help::class,

            TestCommand::class,
        ];
    }

    /**
     * @throws IntentException
     */
    public function __construct()
    {
        // When running local to test I want a different trigger to not trigger both bots
        if (env('APP_ENV') === 'local') {
            $this->prefix = '%';
        }

        $this->discord = new Discord([
                'token' => config('discord.token'),
                'loadAllMembers' => true,
                'intents' => Intents::getDefaultIntents() | Intents::GUILD_VOICE_STATES | Intents::GUILD_MEMBERS |
                    Intents::MESSAGE_CONTENT | Intents::GUILDS
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
            //$this->deleteSlashCommands();
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
    public function getGuild(string $id)
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
            if (method_exists($instance, 'registerMessageCommand')) {
                $instance->registerMessageCommand();
            }
            if (method_exists($instance, 'registerSlashCommand'))
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

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

}
