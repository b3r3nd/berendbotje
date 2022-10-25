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
use App\Discord\Help;
use App\Discord\Music\Pause;
use App\Discord\Music\AddSong;
use App\Discord\Music\Play;
use App\Discord\Music\PlayLocalFile;
use App\Discord\Music\PlayYoutube;
use App\Discord\Music\Queue;
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
use App\Discord\Timeout\AllTimeouts;
use App\Discord\Timeout\DetectTimeouts;
use App\Discord\Timeout\SingleUserTimeouts;
use App\Models\Reaction;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\Interactions\Command\Choice;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\Activity;
use Discord\WebSockets\Intents;
use Discord\Parts\Interactions\Command\Command;

/**
 * We only ever have one instance of this class, you could call it a singleton however it isn't really. This class
 * will always be instantiated when the bot boots. We do not need to check whether that is the case
 * on static function calls. If the instance is not there, everything is broken already.
 *
 * I could pass this as an argument to literally every class constructor (which I did at first) but it became
 * cumbersome rather quick, hence this implementation.
 */
class Bot
{
    private Discord $discord;
    private string $prefix = '$';
    private array $deletedCommands = [];
    private array $deletedReactions = [];
    private static self $instance;
    public array $commandArray;


    /**
     * @return Bot|static
     */
    public static function get(): Bot|static
    {
        return self::$instance;
    }

    public static function getDiscord(): Discord
    {
        return self::$instance->discord;
    }


    /**
     * @throws IntentException
     */
    public function __construct()
    {
        $this->discord = new Discord([
                'token' => config('discord.token'),
                'loadAllMembers' => true,
                'intents' => Intents::getDefaultIntents() | Intents::GUILD_VOICE_STATES | Intents::GUILD_MEMBERS |
                    Intents::MESSAGE_CONTENT | Intents::GUILDS
            ]
        );

        $this->commandArray = [
            AdminIndex::class,
            AddAdmin::class,
        ];

        $this->discord->on('ready', function (Discord $discord) {
            $activity = new Activity($this->discord, [
                'type' => Activity::TYPE_WATCHING,
                'name' => __('bot.status'),
            ]);

            $discord->updatePresence($activity);

            $this->loadCommands();
          //  $this->loadSlashCommands();
        });

        self::$instance = $this;
        return $this;
    }


    private function loadSlashCommands()
    {
        $this->discord->application->commands->freshen()->done(function ($commands) {
            foreach ($commands as $command) {
                $this->discord->application->commands->delete($command);
            }
        });

        $command = new Command($this->discord, [
            'name' => 'slashtest',
            'description' => 'Increase the cringe counter for someone',
            'options' => [
                [
                    'name' => 'user_mention',
                    'description' => 'Mention',
                    'type' => Option::USER,
                    'required' => true,
                ],
                [
                    'name' => 'access_level',
                    'description' => 'Access',
                    'type' => Option::INTEGER,
                    'required' => true,
                ]
            ]
        ]);

        $this->discord->listenCommand('slashtest', function (Interaction $interaction) {
            $embed = EmbedBuilder::create($this->discord)
                ->setTitle('Fields')
                ->setFooter('Je moeder')
                ->getEmbed();
            foreach ($interaction->data->options as $option) {
                $embed->addField(['name' => $option->name, 'value' => $option->value]);
            }
            $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($embed));
        });



        $this->discord->application->commands->save($command);

    }

    private function loadCommands(): void
    {
        new VoiceStateUpdate();
        new DetectTimeouts();
        new BumpCounter();

        foreach (\App\Models\Command::all() as $command) {
            SimpleCommand::create($this, $command->trigger, $command->response);
        }

        foreach (Reaction::all() as $reaction) {
            SimpleReaction::create($this, $reaction->trigger, $reaction->reaction);
        }

        (new Help())->register();
        (new AddAdmin())->register();
        (new DelAdmin())->register();
        (new UpdateAdmin())->register();
        (new AdminIndex())->register();

        (new BumpStatistics())->register();

        (new AddCringe())->register();
        (new DelCringe())->register();
        (new CringeIndex())->register();

        (new AddCommand())->register();
        (new DelCommand())->register();
        (new CommandIndex())->register();


        (new ReactionsIndex())->register();
        (new AddReaction())->register();
        (new DelReaction())->register();


        (new AllTimeouts())->register();
        (new SingleUserTimeouts())->register();

        (new Say())->register();

        (new PlayLocalFile())->register();
        (new PlayYoutube())->register();

        (new AddSong())->register();
        (new Pause())->register();
        (new Stop())->register();
        (new Queue())->register();
        (new Resume())->register();
        (new Play())->register();

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
