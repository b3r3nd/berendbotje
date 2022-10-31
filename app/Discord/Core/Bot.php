<?php

namespace App\Discord\Core;

use App\Discord\Admin\CreateAdmin;
use App\Discord\Admin\AdminIndex;
use App\Discord\Admin\DeleteAdmin;
use App\Discord\Admin\MyAccess;
use App\Discord\Admin\UpdateAdmin;
use App\Discord\Bump\BumpCounter;
use App\Discord\Bump\BumpStatistics;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\Command\SlashCommand;
use App\Discord\Core\Settings\Settings;
use App\Discord\Core\Settings\UpdateSetting;
use App\Discord\Cringe\IncreaseCringe;
use App\Discord\Cringe\CringeIndex;
use App\Discord\Cringe\DecreaseCringe;
use App\Discord\Cringe\ResetCringe;
use App\Discord\Fun\EightBall;
use App\Discord\Fun\MentionResponder;
use App\Discord\Fun\UrbanDictionary;
use App\Discord\Help;
use App\Discord\MediaFilter\CreateMediaChannel;
use App\Discord\MediaFilter\DeleteMediaChannel;
use App\Discord\MediaFilter\MediaChannelIndex;
use App\Discord\MediaFilter\MediaFilter;
use App\Discord\Music\AddSong;
use App\Discord\Music\Pause;
use App\Discord\Music\Play;
use App\Discord\Music\Queue;
use App\Discord\Music\RemoveSong;
use App\Discord\Music\Resume;
use App\Discord\Music\Stop;
use App\Discord\Say;
use App\Discord\SimpleCommand\CreateCommand;
use App\Discord\SimpleCommand\CommandIndex;
use App\Discord\SimpleCommand\DeleteCommand;
use App\Discord\SimpleCommand\SimpleCommand;
use App\Discord\SimpleReaction\CreateReaction;
use App\Discord\SimpleReaction\DeleteReaction;
use App\Discord\SimpleReaction\ReactionIndex;
use App\Discord\SimpleReaction\SimpleReaction;
use App\Discord\Statistics\ModeratorStatistics;
use App\Discord\Statistics\DetectKicksAndBans;
use App\Discord\Statistics\EmoteCounter;
use App\Discord\Statistics\EmoteIndex;
use App\Discord\Statistics\MessageCounter;
use App\Discord\Statistics\MessagesIndex;
use App\Discord\Statistics\ProcessAuditLog;
use App\Discord\Statistics\UserMessages;
use App\Discord\Timeout\AllTimeouts;
use App\Discord\Timeout\DetectTimeouts;
use App\Discord\Timeout\SingleUserTimeouts;
use App\Models\MediaChannel;
use App\Models\Reaction;
use App\Models\Setting;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\User\Activity;
use Discord\WebSockets\Intents;
use Exception;
use League\CommonMark\Extension\Mention\Mention;

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
 * @property $mediaChannel      List of channels marked as media, add or remove any channels whenever you like.
 *
 * When a new command or reaction is added a new instance of either class is instantiated. I cannot manually destroy
 * these instances when the command or reaction is deleted, so I keep track of them here and make sure they do not fire.
 * @see SimpleCommand
 * @see SimpleReaction
 * @property $deletedCommands   List deleted commands so they do not trigger.
 * @property $deletedReactions  List of deleted reactions so they do not rigger.
 *
 * @TODO find better solution for deleted commands and reactions.. probably step away from having a single instance per trigger
 */
class Bot
{
    private Discord $discord;
    private string $prefix = '$';
    private static self $instance;
    private array $mediaChannels = [];
    private array $deletedCommands = [];
    private array $deletedReactions = [];
    private array $settings = [];
    private array $lastMessages = [];


    /**
     * Define all events that do not require commands to be triggered, for example the media filter or voice states.
     * @return string[]
     */
    private function coreClasses(): array
    {
        return [
            VoiceStateUpdate::class,
            DetectTimeouts::class,
            DetectKicksAndBans::class,
            BumpCounter::class,
            EmoteCounter::class,
            MediaFilter::class,
            MentionResponder::class,
            MessageCounter::class,
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
            CreateAdmin::class, DeleteAdmin::class, UpdateAdmin::class, AdminIndex::class, MyAccess::class,
            IncreaseCringe::class, DecreaseCringe::class, CringeIndex::class, ResetCringe::class,
            CreateCommand::class, DeleteCommand::class, CommandIndex::class,
            ReactionIndex::class, CreateReaction::class, DeleteReaction::class,
            CreateMediaChannel::class, DeleteMediaChannel::class, MediaChannelIndex::class,
            AddSong::class, Pause::class, Stop::class, Queue::class, Resume::class, Play::class, RemoveSong::class,
            AllTimeouts::class, SingleUserTimeouts::class,
            Help::class,
            BumpStatistics::class,
            EmoteIndex::class,
            EightBall::class,
            UrbanDictionary::class,
            Say::class,
            Settings::class, UpdateSetting::class,
            UserMessages::class, MessagesIndex::class,
            ModeratorStatistics::class,
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
            $this->loadSettings();
            $this->loadCoreClasses();
            //$this->deleteSlashCommands();
            $this->loadCommands();
        });
        self::$instance = $this;
        return $this;
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
    public function loadSettings(): void
    {
        foreach (Setting::all() as $setting) {
            $this->setSetting($setting->key, $setting->value);
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
                (new $class())->registerSlashCommand();
        }

        // Custom commands
        foreach (\App\Models\Command::all() as $command) {
            SimpleCommand::create($this, $command->trigger, $command->response);
        }

        // Custom reactions
        foreach (Reaction::all() as $reaction) {
            SimpleReaction::create($this, $reaction->trigger, $reaction->reaction);
        }

        // Set media channel filters
        foreach (MediaChannel::all() as $channel) {
            $this->mediaChannels[$channel->channel] = $channel->channel;
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
     * @param string $userId
     * @return Carbon
     */
    public function getLastMessage(string $userId): Carbon
    {
        if (isset($this->lastMessages[$userId])) {
            return $this->lastMessages[$userId];
        }
        return Carbon::now()->subMinutes(100);
    }

    /**
     * @param string $userId
     * @return void
     */
    public function setLastMessage(string $userId): void
    {
        $this->lastMessages[$userId] = Carbon::now();
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param string $setting
     * @param $value
     * @return void
     */
    public function setSetting(string $setting, $value): void
    {
        $this->settings[$setting] = $value;
    }

    /**
     * @param string $setting
     * @return false|mixed
     */
    public function getSetting(string $setting)
    {
        if (isset($this->settings[$setting])) {
            return $this->settings[$setting];
        }
        return false;
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
     * @param string $channel
     * @return void
     */
    public function addMediaChannel(string $channel): void
    {
        $this->mediaChannels[$channel] = $channel;
    }

    /**
     * @param string $channel
     * @return void
     */
    public function delMediaChannel(string $channel): void
    {
        unset($this->mediaChannels[$channel]);
    }

    /**
     * @return array
     */
    public function getMediaChannels(): array
    {
        return $this->mediaChannels;
    }

    /**
     * @return array
     */
    public function getDeletedReactions(): array
    {
        return $this->deletedReactions;
    }

    /**
     * @param string $command
     * @return void
     */
    public function deleteReaction(string $command): void
    {
        $this->deletedReactions[] = $command;
    }

    /**
     * @return array
     */
    public function getDeletedCommands(): array
    {
        return $this->deletedCommands;
    }

    /**
     * @param string $command
     * @return void
     */
    public function deleteCommand(string $command): void
    {
        $this->deletedCommands[] = $command;
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
