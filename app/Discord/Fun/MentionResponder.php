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
    private array $lastResponses = [];


    /**
     * @param array $array
     * @return mixed
     * @throws \Exception
     */
    public function getRandom(array $array): mixed
    {
        $response = $array[random_int(0, (count($array) - 1))];
        while (isset($this->lastResponses[$response])) {
            $response = $array[random_int(0, (count($array) - 1))];
            if (count($this->lastResponses) === count($array)) {
                $this->lastResponses = [];
                break;
            }
        }
        $this->lastResponses[$response] = Carbon::now();
        return $response;
    }

    public function __construct()
    {
        $nsfwOptions = [
            "You should be embarrassed to have that role... its not safe (for work).",
            "Please. Remove your naked obscenity from this discord of civilized folk.",
            "Enough already. Go back to NSFW. You're making people nervous.",
            "You must either be crazy or supremely confident to have the NSFW role without shame.",
            "NSFW.. Not my thing, but who am I to judge...",
            "Oh, dear. Would you look at that? NSFW? How embarrassing.",
            "NSFW role huh? Was it your ma or pa that left you?",
            "Keep your hands to yourself, pervert.",
        ];
        $weebOptions = [
            "Weebs are not welcome in the general chat, so they make their camps in the #weeb channel.",
            "Weebs have no business in this chat, outlander.",
            "And why should I speak with you? You are a weeb.",
        ];
        $highLevelOptions = [
            "These sands are cold, but I feel warmness from your presence.",
            "May the gods watch over your high rank on this server, friend.",
            "Well, look at you. If only everyone was as active in the server as you.",
            "Good to see this server still has such fine and active people. You give me hope.",
            "I hope your parents are proud of you. Although looking at how much XP you got in this server.. I got my doubts",
            "May your next rank be the king of the nerds, dethrone rickert!",
            "Good to see you. At least you know how to use this discord properly.",
            "Wow. Nice rank! Can I have it? I promise I give it back. Honest!",
            "So you're interested in becoming king of the nerds?"
        ];
        $lowLevelOptions = [
            "Disrespect the rules, and you disrespect me.",
            "Don't ask too many questions. Safer for everyone that way.",
            "First time here? Take my advice. You see anything, don't get involved. The moderators will take care of it.",
            "This server is under my protection. You watch yourself, now.",
            "Ah, so you're new here, then? Welcome, I suppose.",
            "Keep your nose clean, and you won't have any problems with me.",
            "Look at all the newcomers! I want to go muting! Or banning!",
        ];
        $kingOfTheNerdsOptions = [
            "Being king of the nerds is a perfectly valid way of life, don't let anyone tell you otherwise.",
            "Some people call you nolife. Me? I call you king of the nerds.",
            "Being king of the nerds is a perfectly valid way of life, don't let anyone tell you otherwise.",
            "Some people call you nolife. Me? I call you king of the nerds.",
            "Being king of the nerds is a perfectly valid way of life, don't let anyone tell you otherwise.",
            "Some people call you nolife. Me? I call you king of the nerds.",
            "Being king of the nerds is a perfectly valid way of life, don't let anyone tell you otherwise.",
            "Some people call you nolife. Me? I call you king of the nerds.",
        ];
        $bumperEliteOptions = [
            "Psst! Hey. I know who you are. Hail the bumper elite!",
            "I've been wanting to say thanks for bumping. Here. It's not much, but at least you get the bumper elite role.",
            "Psst! Hey. I know who you are. Hail the bumper elite!",
            "I've been wanting to say thanks for bumping. Here. It's not much, but at least you get the bumper elite role.",
            "Psst! Hey. I know who you are. Hail the bumper elite!",
            "I've been wanting to say thanks for bumping. Here. It's not much, but at least you get the bumper elite role.",
            "Psst! Hey. I know who you are. Hail the bumper elite!",
            "I've been wanting to say thanks for bumping. Here. It's not much, but at least you get the bumper elite role.",
            "Psst! Hey. I know who you are. Hail the bumper elite!",
        ];
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
            "Filth. Run to the horizon before I hunt you down.",
            "I used to be a trashtalker like you, then I took an banhammer to my face.",
            "By Ysmir.. what?",
            "Get away from me.",
            "So you know how to tag me? Am I supposed to be impressed?",
            "I am this close to muting you, I swear it.",
            "Got something to say?",
            "Shameful, really. I blame the parents.",
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
            "It is our most favored strijder.",
            "Power. You have it, as do all strijders. But power is inert without action and choice.",

        ];
        $bumpOptions = [
            "By Shor, you really bump a lot.",
            "The Gods blessed you with two hands, and you use both for bumping this discord, I can respect that.",
            "Well, look at you. If only everyone bumped like you did.",
            "Good to see you. At least you know how bump the discord properly.",
            "Wow, look at that bump counter, are you a wizard?",
            "You're someone who can get things done, like bumping the discord. I like that.",
            "Hail bumper, bump me up a warm bed would you?",
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
            "I bet you could mute any one of those mean users. I bet you could do anything.",
            "Good to have you by my side, friend. I need reliable moderators around.",
            "you know the old saying: When life gives you lemons, go ban some trolls.",
        ];
        $nonStrijderOptions = [
            "Do you join the strijders vc very often? Oh, what am I saying, of course you don't.",
            "Let me guess... someone stole your sweetroll.",
            "What are you starin' at?",
            "Come, come. I haven't got all day.",
            "And what might you need? Hmm?",
            "Don't bother the other moderators.",
            "You want something from me?",
            "Make it quick.",
            "Out with it.",
        ];
        $options = [
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
            "Got to thinking... maybe I'm the owner, and I just don't know it yet?",
            "Yes, sera?",
            "Hmph.",
            "Aye.",
            "Yeah?",
            "Let's hear it.",
            "If I had a sister, I'd sell her in a second.",
            "Hmm?",
            "Tidings.",
            "I don't owe you money, do I?",
            "Looking to protect yourself, or deal some damage? Wait what did I just say? ._.",
            "The Dutch are so serious about hair. So much hair. Berend thinks they wish they had a glorious bald head like him.",
            "I do swear my blood and honour to the service of the mods.",
            "I’m not a man, I’m a bot in human form. Just release me and point me at the trolls.",
            "I like living here. Berend is so pretty.",
            "The strijders channel looks so pretty. I wish they'd let me inside...",
            "I climbed all the way up to the strijders channel, but they said I wasn't good enough to get in. They're just mean.",
            "You are such a milk-drinker. You can't even stand up to a bot.",
            "Martijn let me see his basement. It's really nice.",
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
            "My God, you're hauling around a lot of cringe.",
            "And the troll was boastful no more, when his ugly head rolled around on the floor!",

        ];

        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use (
            $options, $adminOptions, $cringeOptions, $strijderOptions, $mutedOptions, $bumpOptions,
            $nsfwOptions, $weebOptions, $highLevelOptions, $lowLevelOptions, $kingOfTheNerdsOptions, $bumperEliteOptions,
            $nonStrijderOptions,
        ) {

            if ($message->author->bot) {
                return;
            }
            if (!$message->guild_id) {
                return;
            }

            foreach ($this->lastResponses as $lastResponse => $date) {
                $now = Carbon::now();
                if ($now->diffInSeconds($date) > 60) {
                    unset($this->lastResponses[$lastResponse]);
                }
            }


            if ($message->mentions->count() > 0) {
                if (str_contains($message->content, $discord->user->id)) {

                    // These responses ignore the cooldown of 10 seconds and always trigger.
                    if (str_contains($message->content, '?give')) {
                        $message->reply('Thanks! 😎');
                        return;
                    }

                    $responses = [];
                    if (DiscordUser::hasPermission($message->author->id, $message->guild_id, Permission::TIMEOUTS->value)) {
                        $responses = array_merge($responses, $adminOptions);
                    }
                    $rolesCollection = collect($message->member->roles);

                    // Strijder role
                    if ($rolesCollection->contains('id', "1008136015149531278")) {
                        $responses = array_merge($responses, $strijderOptions);
                    } else {
                        $responses = array_merge($responses, $nonStrijderOptions);
                    }
                    // NSFW role
                    if ($rolesCollection->contains('id', "985997740356018257")) {
                        $responses = array_merge($responses, $nsfwOptions);
                    }
                    // Weeb role
                    if ($rolesCollection->contains('id', "921387543268831273")) {
                        $responses = array_merge($responses, $weebOptions);
                    }
                    // King of the nerds role
                    if ($rolesCollection->contains('id', "598945103431860256")) {
                        $responses = array_merge($responses, $kingOfTheNerdsOptions);
                    }
                    // Bumper Elite role
                    if ($rolesCollection->contains('id', "995771835767607366")) {
                        $responses = array_merge($responses, $bumperEliteOptions);
                    }

                    // Minimal Legendary member options
                    if ($rolesCollection->contains('id', "602121042730680339")) {
                        $responses = array_merge($responses, $highLevelOptions);
                    }
                    // Max active member options (Master member ID)
                    if (!$rolesCollection->contains('id', "602120702002200576")) {
                        $responses = array_merge($responses, $lowLevelOptions);
                    }

                    $discordUser = DiscordUser::get($message->author->id);
                    $cringeCounter = $discordUser->cringeCounters()->where('guild_id', Guild::get($message->guild_id)->id)->get()->first()->count ?? 0;
                    $bumpCounter = $discordUser->bumpCounters()->where('guild_id', Guild::get($message->guild_id)->id)->selectRaw('*, sum(count) as total')->first();
                    $timeoutCounter = Timeout::byGuild($message->guild_id)->where(['discord_id' => $message->author->id])->count();

                    if ($bumpCounter->total > 100) {
                        $responses = array_merge($responses, $bumpOptions);
                    }
                    if ($timeoutCounter > 1) {
                        $responses = array_merge($responses, $mutedOptions);
                    }
                    if ($cringeCounter > 10) {
                        $responses = array_merge($responses, $cringeOptions);
                    }

                    $responses = array_merge($responses, $options);

                    $message->reply($this->getRandom($responses));
                }
            }
        });
    }
}
