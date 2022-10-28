<?php

namespace App\Discord\Core;

use App\Discord\Admin\AddAdmin;
use App\Discord\Admin\AdminIndex;
use App\Discord\Admin\DelAdmin;
use App\Discord\Admin\UpdateAdmin;
use App\Discord\Bump\BumpCounter;
use App\Discord\Bump\BumpStatistics;
use App\Discord\Cringe\AddCringe;
use App\Discord\Cringe\CringeIndex;
use App\Discord\Cringe\DelCringe;
use App\Discord\Cringe\ResetCringe;
use App\Discord\Help;
use App\Discord\MediaFilter\AddMediaChannel;
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
use App\Discord\SimpleCommand\AddCommand;
use App\Discord\SimpleCommand\CommandIndex;
use App\Discord\SimpleCommand\DelCommand;
use App\Discord\SimpleCommand\SimpleCommand;
use App\Discord\SimpleReaction\AddReaction;
use App\Discord\SimpleReaction\DelReaction;
use App\Discord\SimpleReaction\ReactionsIndex;
use App\Discord\SimpleReaction\SimpleReaction;
use App\Discord\Statistics\EmoteCounter;
use App\Discord\Statistics\EmoteIndex;
use App\Discord\Timeout\AllTimeouts;
use App\Discord\Timeout\DetectTimeouts;
use App\Discord\Timeout\SingleUserTimeouts;
use App\Models\MediaChannel;
use App\Models\Reaction;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\User\Activity;
use Discord\WebSockets\Intents;

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


    /**
     * Define all command classes which support both text and slash commands here!
     * @return string[]
     * @see SlashCommand
     */
    private function slashCommands(): array
    {
        return [
            AddAdmin::class,
            DelAdmin::class,
            UpdateAdmin::class,
            AdminIndex::class,
            BumpStatistics::class,
            AddCringe::class,
            DelCringe::class,
            CringeIndex::class,
            AddCommand::class,
            DelCommand::class,
            CommandIndex::class,
            ReactionsIndex::class,
            AddReaction::class,
            DelReaction::class,
            Help::class,
            EmoteIndex::class,
            ResetCringe::class,
            AddMediaChannel::class,
            DeleteMediaChannel::class,
            MediaChannelIndex::class,
        ];
    }

    /**
     * Define all command classes using only text commands here.
     * @return string[]
     * @see Command
     */
    private function textCommands(): array
    {
        return [
            AllTimeouts::class,
            SingleUserTimeouts::class,
            AddSong::class,
            Pause::class,
            Stop::class,
            Queue::class,
            Resume::class,
            Play::class,
            Say::class,
            RemoveSong::class,
        ];
    }

    /**
     * Define all other classes. Mainly events that do not require commands to be triggered, for example on user timeout,
     * or voice state change.
     * @return string[]
     */
    private function coreClasses(): array
    {
        return [
            VoiceStateUpdate::class,
            DetectTimeouts::class,
            BumpCounter::class,
            EmoteCounter::class,
            MediaFilter::class,
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
            $this->loadCommands();
            $this->loadSlashCommands();
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
     * @TODO Deleting and adding everything on boot is not recommended. Need improve this :)
     * @return void
     */
    private function loadSlashCommands(): void
    {
//        $this->discord->application->commands->freshen()->done(function ($commands) {
//            foreach ($commands as $command) {
//                $this->discord->application->commands->delete($command);
//            }
//        });

        foreach ($this->slashCommands() as $class) {
            $instance = new $class();
            $instance->registerMessageCommand();
            $instance->registerSlashCommand();
        }
    }

    /**
     * @return void
     */
    private function loadCommands(): void
    {
        foreach ($this->textCommands() as $class) {
            (new $class())->register();
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
