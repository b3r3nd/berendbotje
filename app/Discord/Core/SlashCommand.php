<?php

namespace App\Discord\Core;

use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;

/**
 * All commands are single classes. They will all extend this class! It will handle some basic filters so the
 * child only has to worry about the actual custom code required for the command instead of rewriting the same
 * 20 lines of code each time.
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
        Bot::getDiscord()->application->commands->save($command);
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
                if ($this->requiresMention) {
                    if ($message->mentions->count() == 0) {
                        if (isset($this->usageString)) {
                            $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString));
                        } else {
                            $message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.provide-mention')));
                        }
                        return;
                    }
                }
                if ($this->requiredArguments > 0) {
                    $parameters = explode(' ', $message->content);
                    if (!isset($parameters[$this->requiredArguments])) {
                        if (isset($this->usageString)) {
                            $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString));
                        } else {
                            $message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.provide-arguments', ['count' => $this->requiredArguments])));
                        }
                        return;
                    } else {
                        array_shift($parameters);
                        $this->messageString = join(' ', $this->arguments);

                        foreach ($parameters as $index => $parameter) {
                            if ($parameter == $message->mentions->first()) {
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
}
