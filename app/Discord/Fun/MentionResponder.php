<?php

namespace App\Discord\Fun;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Permission;
use App\Models\DiscordUser;
use App\Models\Guild;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MentionResponder
{

    private array $lastMessages = [];

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

    public function __construct()
    {
        $adminOptions = [
            "Everything's in order.",
            "Staying safe I hope.",
            "Yes boss? How can I help you?",
            "Whatever you need. Just say the word.",
            "I hope you're finding the server in proper order.",
            "I trust the day's found you well.",
            "You have the owners confidence, friend. And so you have mine.",
        ];
        $options = [
            "Let me guess... someone stole your sweetroll.",
            "Disrespect the rules, and you disrespect me.",
            "Trouble?",
            "What is it?",
            "No lollygaggin'.",
            "Gotta keep my eyes open. Damn trolls could appear at any time.",
            "My cousin's out generating code, and what do I get? discord mod duty.",
            "Fear not. Come troll or scammers, we'll be ready.",
            "Everything all right?",
            "I'd be a lot warmer and a lot happier with a bellyful of mead...",
            "Watch the skies, traveler.",
            "You hear that? I swear, there's something out there. In the dark.",
            "This server is under my protection. You watch yourself, now.",
            "First time here? Take my advice. You see anything, don't get involved. The moderators will take care of it.",
            "Don't ask too many questions. Safer for everyone that way.",
            "Keep your nose clean, and you won't have any problems with me.",
            "Got to thinking... maybe I'm the owner, and I just don't know it yet?",
        ];

        $cringeOptions = [
            "Uch. Been tending your hounds? You smell like a wet dog.",
            "Wait... I know you.",
            "You come up to me? You looking for a beating?",
            "I need to ask you to stop. That... cringe shit.. is making people nervous.",
            "Ugh. you stink of death. You been grave robbing?",
            "Ugh. Your breath is foul. What've you been eating?",
            "Try to hide it all you want. I know about your cringe counter... and so do the other mods.",
        ];

        $timeoutOptions = [
            "By Ysmir.. what?",
            "Get away from me.",
            "So you know how to tag me? Am I supposed to be impressed?",
            "I am this close to muting you, I swear it."
        ];


        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($options, $adminOptions, $cringeOptions, $timeoutOptions) {
            if ($message->author->bot) {
                return;
            }

            if ($message->mentions->count() > 0) {
                if (str_contains($message->content, $discord->user->id)) {

                    // These responses ignore the cooldown of 10 seconds and always trigger.
                    if (str_contains($message->content, '?give')) {
                        $message->reply('Thanks! 😎');
                        return;
                    } else if (str_contains($message->content, Bot::get()->getPrefix() . 'addcringe')) {
                        $message->reply('Keep on dreaming.. 🖕');
                        return;
                    } elseif (DiscordUser::hasPermission($message->author->id, $message->guild_id, Permission::TIMEOUTS->value)) {
                        $message->reply($adminOptions[rand(0, (count($adminOptions) - 1))]);
                        return;
                    }

                    $lastMessageDate = $this->getLastMessage($message->author->id);

                    if ($lastMessageDate->diffInSeconds(Carbon::now()) <= 5) {
                        // Annoying response when you trigger it quickly
                        $message->reply($timeoutOptions[rand(0, (count($timeoutOptions) - 1))]);

                    } else if ($lastMessageDate->diffInSeconds(Carbon::now()) >= 30) {
                        $this->setLastMessage($message->author->id);
                        $discordUser = DiscordUser::get($message->author->id);
                        $cringeCounter = $discordUser->cringeCounters()->where('guild_id', Guild::get($message->guild_id)->id)->get()->first()->count ?? 0;

                        if ($cringeCounter > 10) {
                            $message->reply($cringeOptions[rand(0, (count($cringeOptions) - 1))]);
                        } else {
                            $message->reply($options[rand(0, (count($options) - 1))]);
                        }
                    }
                }
            }
        });
    }
}
