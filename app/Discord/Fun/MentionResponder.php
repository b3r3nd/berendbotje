<?php

namespace App\Discord\Fun;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Permission;
use App\Models\Bumper;
use App\Models\DiscordUser;
use App\Models\Guild;
use App\Models\Timeout;
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

    /**
     * @param array $array
     * @return mixed
     */
    public function getRandom(array $array): mixed
    {
        return $array[rand(0, (count($array) - 1))];
    }


    /**
     * Restoration is a perfectly valid school of magic, and don't let anyone tell you otherwise!
     * What is better - to be born good, or to overcome your evil nature through great effort?"
     * So, You Wish To Master The Arcane Arts.
     * A guard might get nervous, a woman approaches with a weapon drawn...
     * Some People Call This Junk. Me? I Call It Treasure
     * Looking to protect yourself, or deal some damage?
     * So, You Wish To Master The Arcane Arts.
     * Those Warriors From Hammerfell? They've Got Curved Swords. Curved Swords!"
     */


    public function __construct()
    {
        $mutedOptions = [
            "What do you want? You've caused enough trouble.",
            "I don't like talking to someone who holds their honor so cheaply.",
            "I should bash your face in after all you've done.",
            "Another abuser, here to lick the admins boots. Good job.",
            "Maybe you're not supposed to be here.",
            "I've got my eyes on you.",
            "Hey, you. You're finally awake. You were muted, right? Walked right into that ambush.",
            "Filth. Run to the horizon before I hunt you down.",
            "Absolutely no time to deal with lowlifes these days. Go away.",
        ];

        $strijderOptions = [
            "This discord belongs to the strijders!!!",
            "You've been a good friend to me. That means something.",
            "I fight so that all the fighting I've already done hasn't been for nothing. I fight… because I must. Real STRIJDERS!!",
            "Now here's a strijder I'm glad to see.",
            "Strijders like you are hard to find and very valuable to me.",
            "Divines bless you. May the ground you walk quake as you pass.",
            "We're one of the same kind, you and I. I'm glad to have met you.",
            "My favorite strijder. Let's get some mead.",
            "Divines smile on you, strijder.",
            "May the gods watch over your messages, friend",
            "You and me, we're the only people around who aren't complete fools.",
        ];

        $bumpOptions = [
            "By Shor, you really bump a lot.",
            "Psst! Hey. I know who you are. Hail the bumper elite!",
            "The Gods blessed you with two hands, and you use both for bumping this discord, I can respect that.",
            "Well, look at you. If only everyone bumped like you did.",
            "Good to see you. At least you know how bump the discord properly.",
            "Wow, look at that bump counter, are you a wizard?",
        ];


        $adminOptions = [
            "Everything's in order.",
            "Staying safe I hope.",
            "Yes boss? How can I help you?",
            "Whatever you need. Just say the word.",
            "I hope you're finding the server in proper order.",
            "I trust the day's found you well.",
            "You have the owners confidence, friend. And so you have mine.",
            "Good to have you by my side, friend. I need reliable people around.",
            "Good to see you. Finally someone useful is around.",

        ];
        $options = [
            "Do you join the strijders vc very often? Oh, what am I saying, of course you don't.",
            "Out with it.",
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
            "I used to be a trashtalker like you, then I took an banhammer to my face.",
            "And what might you need? Hmm?",
            "Yes, sera?",
            "I don't owe you money, do I?",
            "What are you starin' at?",
            "Hmph.",
            "Aye.",
            "Yeah?",
            "Come, come. I haven't got all day.",
            "Let's hear it.",
            "Don't bother the other moderators.",
            "If I had a sister, I'd sell her in a second.",

        ];

        $cringeOptions = [
            "Uch. Been tending your hounds? You smell like a wet dog.",
            "Wait... I know you.",
            "You come up to me? You looking for a beating?",
            "I need to ask you to stop. That... cringe shit.. is making people nervous.",
            "Ugh. you stink of death. You been grave robbing?",
            "Ugh. Your breath is foul. What've you been eating?",
            "Try to hide it all you want. I know about your cringe counter... and so do the other mods.",
            "Is that... cringe? Coming out of your mouth?",
            "If you are smart you walk away.",
            "Something you need, you miserable wretch?",
            "Oh, it's you. I was wondering why I was smelling something unpleasant.",
            "Every time I think of you, I imagine a Saber Cat closing its claws around your face.",
            "Don't like those eyes you got. There's a bad hunger to them.",
            "Aren't you... embarrassed?",
        ];

        $timeoutOptions = [
            "By Ysmir.. what?",
            "Get away from me.",
            "So you know how to tag me? Am I supposed to be impressed?",
            "I am this close to muting you, I swear it.",
            "Got something to say?",
        ];


        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($options, $adminOptions, $cringeOptions, $timeoutOptions, $strijderOptions, $mutedOptions, $bumpOptions) {
            if ($message->author->bot) {
                return;
            }
            if (!$message->guild_id) {
                return;
            }

            if ($message->mentions->count() > 0) {
                if (str_contains($message->content, $discord->user->id)) {

                    // These responses ignore the cooldown of 10 seconds and always trigger.
                    if (str_contains($message->content, '?give')) {
                        $message->reply('Thanks! 😎');
                        return;
                    }

                    if (DiscordUser::hasPermission($message->author->id, $message->guild_id, Permission::TIMEOUTS->value)) {
                        $message->reply($this->getRandom($adminOptions));
                        return;
                    }
                    $rolesCollection = collect($message->member->roles);
                    if ($rolesCollection->contains('id', "1008136015149531278")) {
                        $message->reply($this->getRandom($strijderOptions));
                        return;
                    }

                    $discordUser = DiscordUser::get($message->author->id);
                    $cringeCounter = $discordUser->cringeCounters()->where('guild_id', Guild::get($message->guild_id)->id)->get()->first()->count ?? 0;
                    $bumpCounter = $discordUser->bumpCounters()->where('guild_id', Guild::get($message->guild_id)->id)->selectRaw('*, sum(count) as total')->first();
                    $timeoutCounter = Timeout::byGuild($message->guild_id)->where(['discord_id' => $message->author->id])->count();

                    if ($bumpCounter->total > 100) {
                        $message->reply($this->getRandom($bumpOptions));
                        return;
                    }
                    if ($timeoutCounter > 1) {
                        $message->reply($this->getRandom($mutedOptions));
                        return;
                    }
                    if ($cringeCounter > 10) {
                        $message->reply($this->getRandom($cringeOptions));
                        return;
                    }


                    $lastMessageDate = $this->getLastMessage($message->author->id);

                    if ($lastMessageDate->diffInSeconds(Carbon::now()) <= 5) {
                        $message->reply($this->getRandom($timeoutOptions));
                    } else if ($lastMessageDate->diffInSeconds(Carbon::now()) >= 30) {
                        $this->setLastMessage($message->author->id);
                        $message->reply($this->getRandom($options));
                    }
                }
            }
        });
    }
}
