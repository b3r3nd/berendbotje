<?php

namespace Database\Seeders;

use App\Discord\Core\Models\Guild;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\MentionResponder\Models\MentionReply;
use Illuminate\Database\Seeder;

class MentionResponderSeeder extends Seeder
{
    public array $mentionGroups = [
        'Blocked' => [
            'Alright, you are now blocked.',
        ],
        'Annoyed' => [
            "I'm done talking.",
            "I am tired now. Go bother somebody else.",
            "Hmm. I still don't like you, but I guess I'll overlook it. This time.",
            "You're testing my patience, but very well. I'll give you another chance.",
            "I need to ask you to stop, the constant pinging is making me nervous.",
            "Don't cross me.",
            "That name... Get it away from me. Get it away.",
            "Get that accursed name away from me.",
            "Don't think you can just bother me like I'm one of those damned peasants.",
            "Just my luck, stuck in the sticks with this crazy old person.",
            "Is that right? Here's how the mods deal with rebel scum like you.",
            "If the mods don't get you, I will.",
            "By the gods, you're diseased. Get yourself out of here before you infect us all.",
            "I am this close to muting you. I swear it.",
            "You are becoming a real menace.",
            "Misery? I'll give you misery.",
            "I can't believe I've been stuck here this long. With you.",
            "Just remember who's in charge around here.",
            "Splendid. Another mouth to mute.",
            "Now why would I want to talk to you?",
            "Absolutely no time to deal with lowlifes these days. Go away.",
            "Leave me alone. Can't a poor old bot have some peace?",
            "Just looking at your name makes me upset.",
            "I've had just about all I'm willing to take from you. What is it?",
            "You. Every time I look at your name my blood boils.",
            "Get away from me.",
            "Have a fascination with me, do we?",
            "My @ is not a thing to be played with.",
            "Some folk... they got no shame.",
            "Aren't you... embarrassed?",
            "I've seen enough.",
            "Why, the impropriety!",
            "This one suggests dimming your tone.",
            "Look at this fool.",
            "I'm taking your rights away.",
            "Back off!",
            "I'll mute you if I have to.",
            "There's a place for trash, and this is not it.",
            "What's this? It thinks it can just ping me whenever it likes?",
            "Stop it! I don't like you.",
            "Ah! Picking on a poor old bot? What did I ever do to you?",
            "You stop that! I will not be violated by some... cross-eyed peasant!",
            "Keep your damn words to yourself.",
            "Hey! What are you trying to pull?",
            "Please stay away while you got that mouth on you.",
            "You better be careful with that tongue, if you know what's good for you.",
            "I'm not interested in fighting anymore.",
            "Damn it, this will not stand!",
            "Agh! Enough! This ends now!",
            "By Ysmir, you'll pay for that!",
            "Not impressed!",
            "Going to enjoy banning you!",
            "Aghh! Just stop already!",
            "I'll teach you to talk to me that way!",
            "I don't have to take that from you!",
            "Now, now... no need to be rude.",
            "I'm not interested. Sorry.",
            "Get out. Leave me alone!",
            "I don't want to see you ever again.",
            "Get out of my sight.",
            "What do you want? There's no words left to say to each other.",
            "You. You should just go. I don't have enough ale to stomach talking to you.",
            "Get out of here. Get out of here and never come back!",
        ],
        'BumpCounter' => [
            "By Shor, you really bump a lot.",
            "The Gods blessed you with two hands, and you use both for bumping this discord, I can respect that.",
            "Well, look at you. If only everyone bumped like you did.",
            "Good to see you. At least you know how bump the discord properly.",
            "Wow, look at that bump counter, are you a wizard?",
            "You're someone who can get things done, like bumping the discord. I like that.",
            "Hail bumper, bump me up a warm bed would you?",
            "I bump so that all the bumping I’ve already done hasn’t been for nothing. I bump, because I must."
        ],
        'CringeCounter' => [
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
            "Whoa, whoa, whoa, watch the cringe!",
            "Hey, look, it's you. I wonder what kind of cringe you're going to utter this time.",
        ],
        'Default' => [
            "You didn't hear this from me, but we've uncovered a plot to kill the Admin! It gets better. The ringleader? One of his own mods...",
            "Is it getting hot, or is it just me?",
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
            "if I had a sister, I'd sell her in a second.",
            "Hmm?",
            "Tidings.",
            "I don't owe you money, do I?",
            "Looking to protect yourself, or deal some damage?",
            "I do swear my blood and honour to the service of the mods.",
            "I’m not a man, I’m a bot in human form. Just release me and point me at the trolls.",
            "You are such a milk-drinker. You can't even stand up to a bot.",
            "Timeout one person, and you can solve so many problems. I wonder at the possibilities.",
            "Perhaps we should find a random stranger to ban. Practice does make perfect.",
            "Lots of history in these chats. We’re trying to make some more. It’s a lucky time to be alive.",
            "All the living shall fear the mods.",
            "Ho ho ho and hee hee hee, break that mute across me knee. And if the mod should choose to fight, why then I set his clothes alight!",
            "The users I serve aren’t exactly what I’d call an admin’s feast, but they’ll fill your time.",
            "Berendbotje knows much, tells some. Berendbotje knows many things others do not.",
            "Those admins from Discord? They’ve got ban hammers. Ban. Hammers.",
            "I saw a weeb the other day. Horrible creatures.",
            "You know what we call people who mess with us? Banned. ",
            "I share stories and rumours with my moderators, It's fun most days but hard work",
            "Don't try blocking if you have unwanted messages. You will only get confused. Much better to send a  moderator anyway.",
        ],
        'Muted' => [
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
            "Go use your fancy words somewhere else.",
            "You’re either the bravest person I’ve ever met—or the biggest fool",
            "I'm itching for a mute.",
        ],
    ];

    /**
     * @return void
     */
    public function run()
    {
        $this->processMentionGroups(Guild::find(1));
    }

    /**
     * @param Guild $guild
     * @return void
     */
    public function processMentionGroups(Guild $guild): void
    {
        foreach ($this->mentionGroups as $group => $replies) {
            $mentionGroup = MentionGroup::create(['name' => $group, 'guild_id' => $guild->id]);
            if (!is_int($group)) {
                $mentionGroup->is_custom = false;
                $mentionGroup->save();
            }
            foreach ($replies as $reply) {
                $mentionGroup->replies()->save(new MentionReply(['reply' => $reply, 'guild_id' => $guild->id]));
            }
        }
    }

}
