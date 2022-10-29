<?php

namespace App\Discord\Core;

use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Exception;

/**
 * Abstract Command class which supports both normal message and slash commands. Slash commands require a lot less coding
 * since parameter validation is already done before the data arrived in the backend. Sad part is tho, the promise which
 * listens to slash commands returns Interaction objects, normal commands return message objects. This is cumbersome
 * because we need to first format this incoming data to a convenient and uniform format the rest of the custom code
 * in its child can use, regardless how the command was triggered.
 *
 * The class will only send Embeds back to discord, nothing else. There is an EmbedBuilder and EmbedFactory to more
 * quickly and easily return Embeds
 * @see EmbedBuilder
 * @see EmbedFactory
 *
 * Properties you can set yourself in child class:
 * @property AccessLevels $accessLevels         Required access level for this command.
 * @property string $trigger                    Trigger for the command, both slash and text.
 * @property string $description                Description for the (slash) command.
 * @property bool $requiresMention              Whether a user mention is required as argument
 * @property int $requiredArguments             The amount of arguments required for the text version of the command.
 * @property string $usageString                Example usage of how to use the command, shown as error on incorrect usage.
 * @property array $slashCommandOptions         Array of all options @see https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-option-structure
 *
 * Properties which will be set for you and available in child:
 * @property string $message                If available set with the Message instance received.
 * @property array $arguments               Array of all the given arguments by either slash or text commands.
 * @property string $messageString          String of the message received without command trigger.
 * @property string $commandUser            Discord ID of user using the command.
 */
abstract class SlashCommand
{
    protected AccessLevels $accessLevel;
    protected Message $message;
    protected string $command;
    protected string $trigger;
    protected string $description;
    protected bool $requiresMention = false;
    protected int $requiredArguments = 0;
    protected string $usageString;
    protected array $slashCommandOptions;
    protected array $arguments = [];
    protected string $messageString = '';
    protected string $commandUser;


    public abstract function accessLevel(): AccessLevels;

    public abstract function trigger(): string;

    public abstract function action(): MessageBuilder;

    public function __construct()
    {
        $this->accessLevel = $this->accessLevel();
        $this->trigger = $this->trigger();
    }


    public function registerSlashCommand(): void
    {
        $optionsArray = [
            'name' => $this->trigger,
            'description' => $this->description ?? $this->trigger
        ];
        if (isset($this->slashCommandOptions)) {
            $optionsArray['options'] = $this->slashCommandOptions;
        }
        $command = new \Discord\Parts\Interactions\Command\Command(Bot::getDiscord(), $optionsArray);
        Bot::getDiscord()->listenCommand($this->trigger, function (Interaction $interaction) {
            $this->arguments = [];
            if (!DiscordUser::hasLevel($interaction->member->id, $this->accessLevel->value)) {
                return $interaction->respondWithMessage(EmbedFactory::failedEmbed(__("bot.lack-access")));
            }
            foreach ($interaction->data->options as $option) {
                $this->arguments[] = $option->value;
            }
            $this->commandUser = $interaction->member->id;
            return $interaction->respondWithMessage($this->action());
        });

        // Why you ask? I do not want to register slash commands everytime on my test bot. It takes time, it's
        // Annoying and I don't need them!
        if (env('APP_ENV') != 'local') {
            Bot::getDiscord()->application->commands->save($command);
        }
    }

    public function registerMessageCommand(): void
    {
        Bot::get()->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            if (str_starts_with($message->content, Bot::get()->getPrefix() . $this->trigger)) {

                if (!DiscordUser::hasLevel($message->author->id, $this->accessLevel->value)) {
                    $message->channel->sendMessage(EmbedFactory::failedEmbed(__("bot.lack-access")));
                    return;
                }
                if ($this->requiresMention && $message->mentions->count() == 0) {
                    $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString ?? __('bot.provide-mention')));
                    return;
                }
                if ($this->requiredArguments > 0) {
                    $parameters = explode(' ', $message->content);
                    if (!isset($parameters[$this->requiredArguments])) {
                        $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString ?? __('bot.provide-arguments', ['count' => $this->requiredArguments])));
                        return;
                    } else {
                        array_shift($parameters);
                        $this->messageString = join(' ', $this->arguments);
                        foreach ($parameters as $index => $parameter) {
                            if ($message->mentions->first() && str_contains($parameter, $message->mentions->first())) {
                                $parameters[$index] = $message->mentions->first()->id;
                            }
                        }
                        $this->arguments = $parameters;
                    }
                }
                $this->message = $message;
                $this->commandUser = $message->author->id;
                $message->channel->sendMessage($this->action());
            }
        });
    }

    /**
     * @return string
     */
    public function getCommandUser(): string
    {
        return $this->commandUser;
    }
}
