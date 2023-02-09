<?php

namespace Database\Seeders;

use App\Models\MentionGroup;
use App\Models\MentionReply;
use Illuminate\Database\Seeder;

class MentionResponderSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {

        $hasRoleGroups = [
            'Blocked' => [
                'Alright, you are now blocked.',
            ],
        ];

//            'Annoyed' => [
//                "I'm done talking.",
//                "I am tired now. Go bother somebody else.",
//                "Hmm. I still don't like you, but I guess I'll overlook it. This time.",
//                "You're testing my patience, but very well. I'll give you another chance.",
//                "I need to ask you to stop, the constant pinging is making me nervous.",
//                "Don't cross me.",
//                "That name... Get it away from me. Get it away.",
//                "Get that accursed name away from me.",
//                "Don't think you can just bother me like I'm one of those damned peasants.",
//                "Just my luck, stuck in the sticks with this crazy old person.",
//                "Is that right? Here's how the mods deal with rebel scum like you.",
//                "If the mods don't get you, I will.",
//                "By the gods, you're diseased. Get yourself out of here before you infect us all.",
//                "Hard to believe I ever complained about Strijders being annoying...",
//                "I am this close to muting you. I swear it.",
//                "You are becoming a real menace.",
//                "Misery? I'll give you misery.",
//                "I can't believe I've been stuck here this long. With you.",
//                "Just remember who's in charge around here.",
//                "Splendid. Another mouth to mute.",
//                "Alright, you are now blocked.",
//                "Now why would I want to talk to you?",
//                "Absolutely no time to deal with lowlifes these days. Go away.",
//                "Leave me alone. Can't a poor old bot have some peace?",
//                "Just looking at your name makes me upset.",
//                "I've had just about all I'm willing to take from you. What is it?",
//                "You. Every time I look at your name my blood boils.",
//                "Get away from me.",
//                "Have a fascination with me, do we?",
//                "My @ is not a thing to be played with.",
//                "Some folk... they got no shame.",
//                "Aren't you... embarrassed?",
//                "I've seen enough.",
//                "Why, the impropriety!",
//                "This one suggests dimming your tone.",
//                "Look at this fool.",
//                "Wait until Berend hears about this.",
//                "I'm taking your rights away.",
//                "Back off!",
//                "I'll mute you if I have to.",
//                "There's a place for trash, and this is not it.",
//                "What's this? It thinks it can just ping me whenever it likes?",
//                "Stop it! I don't like you.",
//                "Ah! Picking on a poor old bot? What did I ever do to you?",
//                "You stop that! I will not be violated by some... cross-eyed peasant!",
//                "Keep your damn words to yourself.",
//                "Hey! What are you trying to pull?",
//                "Please stay away while you got that mouth on you.",
//                "You better be careful with that tongue, if you know what's good for you.",
//                "I'm not interested in fighting anymore.",
//                "Damn it, this will not stand!",
//                "Agh! Enough! This ends now!",
//                "By Ysmir, you'll pay for that!",
//                "Not impressed!",
//                "Going to enjoy banning you!",
//                "Aghh! Just stop already!",
//                "I'll teach you to talk to me that way!",
//                "I don't have to take that from you!",
//                "Now, now... no need to be rude.",
//                "I'm not interested. Sorry.",
//                "Get out. Leave me alone!",
//                "I don't want to see you ever again.",
//                "Get out of my sight.",
//                "What do you want? There's no words left to say to each other.",
//                "You. You should just go. I don't have enough ale to stomach talking to you.",
//                "Get out of here. Get out of here and never come back!",
//            ],
//            'BumpCounter' => [
//                "By Shor, you really bump a lot.",
//                "The Gods blessed you with two hands, and you use both for bumping this discord, I can respect that.",
//                "Well, look at you. If only everyone bumped like you did.",
//                "Good to see you. At least you know how bump the discord properly.",
//                "Wow, look at that bump counter, are you a wizard?",
//                "You're someone who can get things done, like bumping the discord. I like that.",
//                "Hail bumper, bump me up a warm bed would you?",
//                "I bump so that all the bumping I’ve already done hasn’t been for nothing. I bump, because I must."
//            ],
//            'CringeCounter' => [
//                "Uch. Been tending your hounds? You smell like a wet dog.",
//                "Wait... I know you.",
//                "You come up to me? You looking for a beating?",
//                "I need to ask you to stop. That... cringe shit.. is making people nervous.",
//                "Ugh. you stink of death. You been grave robbing?",
//                "Ugh. Your breath is foul. What've you been eating?",
//                "Try to hide it all you want. I know about your cringe counter... and so do the other mods.",
//                "Is that... cringe? Coming out of your mouth?",
//                "If you are smart you walk away.",
//                "Something you need, you miserable wretch?",
//                "Oh, it's you. I was wondering why I was smelling something unpleasant.",
//                "Every time I think of you, I imagine a Saber Cat closing its claws around your face.",
//                "Don't like those eyes you got. There's a bad hunger to them.",
//                "Aren't you... embarrassed?",
//                "My God, you're hauling around a lot of cringe.",
//                "And the troll was boastful no more, when his ugly head rolled around on the floor!",
//                "Whoa, whoa, whoa, watch the cringe!",
//                "Hey, look, it's you. I wonder what kind of cringe you're going to utter this time.",
//            ],
//            'Default' => [
//                "You didn't hear this from me, but we've uncovered a plot to kill the Admin!",
//                "It gets better. The ringleader? One of his own mods...",
//                "Is it getting hot, or is it just me?",
//                "Trouble?",
//                "What is it?",
//                "No lollygaggin'.",
//                "Gotta keep my eyes open. Damn trolls could appear at any time.",
//                "My cousin's out generating code, and what do I get? discord mod duty.",
//                "Fear not. Come troll or scammers, we'll be ready.",
//                "Everything all right?",
//                "I'd be a lot warmer and a lot happier with a bellyful of mead...",
//                "Watch the skies, traveler.",
//                "You hear that? I swear, there's something out there. In the dark.",
//                "Got to thinking... maybe I'm the owner, and I just don't know it yet?",
//                "Yes, sera?",
//                "Hmph.",
//                "Aye.",
//                "Yeah?",
//                "Let's hear it.",
//                "if I had a sister, I'd sell her in a second.",
//                "Hmm?",
//                "Tidings.",
//                "I don't owe you money, do I?",
//                "Looking to protect yourself, or deal some damage?",
//                "The Dutch are so serious about hair. So much hair. Berend thinks they wish they had a glorious bald head like him.",
//                "I do swear my blood and honour to the service of the mods.",
//                "I’m not a man, I’m a bot in human form. Just release me and point me at the trolls.",
//                "I like living here. Berend is so pretty.",
//                "The strijders channel looks so pretty. I wish they'd let me inside...",
//                "I climbed all the way up to the strijders channel, but they said I wasn't good enough to get in. They're just mean.",
//                "You are such a milk-drinker. You can't even stand up to a bot.",
//                "Martijn let me see his basement. It's really nice.",
//                "Timeout one person, and you can solve so many problems. I wonder at the possibilities.",
//                "In their tongue he is Berend - de kale.",
//                "Perhaps we should find a random stranger to ban. Practice does make perfect.",
//                "Lots of history in these chats. We’re trying to make some more. It’s a lucky time to be alive.",
//                "And he says to the man, ‘That’s not a horker! That’s Koen!",
//                "All the living shall fear the mods.",
//                "Ho ho ho and hee hee hee, break that mute across me knee. And if the mod should choose to fight, why then I set his clothes alight!",
//                "The users I serve aren’t exactly what I’d call an admin’s feast, but they’ll fill your time.",
//                "Some people call him Berend. Me? I call him often.",
//                "Berendbotje knows much, tells some. Berendbotje knows many things others do not.",
//                "Those admins from Discord? They’ve got ban hammers. Ban. Hammers.",
//                "You know what's wrong with the Netherlands these days? Everyone is obsessed with Berend and not me.",
//                "I saw a weeb the other day. Horrible creatures.",
//                "for Berend!",
//                "You know what we call people who mess with us? Banned. ",
//                "...What? Berend? Is that your voice I hear? Hmm... No, no... Just my head playing tricks... Foolish me...",
//                "I can talk for a moment. Just a moment. Then Berend must be... tended to.",
//                "I share stories and rumours with my moderators, It's fun most days but hard work",
//                "I always wondered what Berend actually looked like. I hear he's like humans, but without hair.",
//                "Don't try blocking if you have unwanted messages. You will only get confused. Much better to send a  moderator anyway.",
//                "I'd read stories about Berend, but I didn't expect him to be that big!",
//            ],
//            'Muted' => [
//                "What do you want? You've caused enough trouble.",
//                "I don't like talking to someone who holds their honor so cheaply.",
//                "I should bash your face in after all you've done.",
//                "Another abuser, here to lick the admins boots. Good job.",
//                "Maybe you're not supposed to be here.",
//                "I've got my eyes on you.",
//                "Hey, you. You're finally awake. You were muted, right? Walked right into that ambush.",
//                "Filth. Run to the horizon before I hunt you down.",
//                "Absolutely no time to deal with lowlifes these days. Go away.",
//                "Filth. Run to the horizon before I hunt you down.",
//                "I used to be a trashtalker like you, then I took an banhammer to my face.",
//                "By Ysmir.. what?",
//                "Get away from me.",
//                "So you know how to tag me? Am I supposed to be impressed?",
//                "I am this close to muting you, I swear it.",
//                "Got something to say?",
//                "Shameful, really. I blame the parents.",
//                "Go use your fancy words somewhere else.",
//                "You’re either the bravest person I’ve ever met—or the biggest fool",
//                "I'm itching for a mute.",
//            ],
//            // Strijder
//            '1008136015149531278' => [
//                "This discord belongs to the strijders!!!",
//                "You've been a good friend to me. That means something.",
//                "I fight so that all the fighting I've already done hasn't been for nothing. I fight… because I must. Real STRIJDERS!!",
//                "Now here's a strijder I'm glad to see.",
//                "Strijders like you are hard to find and very valuable to me.",
//                "Divines bless you. May the ground you walk on quake as you pass.",
//                "We're one of the same kind, you and I. I'm glad to have met you.",
//                "My favorite strijder. Let's get some mead.",
//                "Divines smile on you, strijder.",
//                "May the gods watch over your messages, friend",
//                "You and me, we're the only people around who aren't complete fools.",
//                "It is our most favored strijder.",
//                "Power. You have it, as do all strijders. But power is inert without action and choice.",
//                "I’ve never seen anything quite like you.",
//                "Yes, strijder? How can I be of service?",
//                "Oh, of course. a strijder needs me, after all."
//            ],
//            // Weeb
//            '921387543268831273' => [
//                "Weebs are not welcome in the general chat, so they make their camps in the #weeb channel.",
//                "Weebs have no business in this chat, outlander.",
//                "And why should I speak with you? You are a weeb.",
//                "These Weebs don’t even have the decency to dress properly.",
//                "Enough! I will not stand idly by while a Weeb runs wild and infects my people!",
//                "You have opened the door to darkness, little man.",
//                "I can be trusted. I know this. But they do not. Onikaan ni ov weebs. It is always wise to mistrust a weeb.",
//                "You'll make a fine rug, Weeb!",
//                "I'll show you to your corner. Right this way.",
//                "You should have stayed in your filthy channel, weeb!",
//                "I was always taught to avoid these types of people. I think I see why, now.",
//                "Weebs... an unclean lot. It will be a pleasure to clear this place.",
//                "Tell me you've seen them. Those... things. Small and evil, like something out of a nightmare.",
//                "They come from the Weeb channel. I know it.",
//                "Weebs. There oughta be a law against them.",
//                "We've got a Weeb on the loose...",
//                "A...a Weeb! How absolutely terrible.",
//            ],
//            // NSFW
//            '985997740356018257' => [
//                "You should be embarrassed to have that role... its not safe (for work).",
//                "Please. Remove your naked obscenity from this discord of civilized folk.",
//                "Enough already. Go back to NSFW. You're making people nervous.",
//                "You must either be crazy or supremely confident to have the NSFW role without shame.",
//                "NSFW.. Not my thing, but who am I to judge...",
//                "Oh, dear. Would you look at that? NSFW? How embarrassing.",
//                "NSFW role huh? Was it your ma or pa that left you?",
//                "Keep your hands to yourself, pervert.",
//                "Either I’m drunk, or you’re naked. Possibly both.",
//                "If you're not going use that dignity you dropped... do you mind if I take it?",
//            ],
//            // Legendary
//            '602121042730680339' => [
//                "These sands are cold, but I feel warmness from your presence.",
//                "May the gods watch over your high rank on this server, friend.",
//                "Well, look at you. If only everyone was as active in the server as you.",
//                "Good to see this server still has such fine and active people. You give me hope.",
//                "I hope your parents are proud of you. Although looking at how much XP you got in this server.. I got my doubts",
//                "May your next rank be the king of the nerds, dethrone rickert!",
//                "Good to see you. At least you know how to use this discord properly.",
//                "Wow. Nice rank! Can I have it? I promise I give it back. Honest!",
//                "So you're interested in becoming king of the nerds?",
//                "You've shown yourself mighty, both in Voice and messages. In order to defeat the admins, you've gained mastery of dreadful weapons. Now it is up to you to decide what to do with your power and skill. Your future lies before you.",
//                "Oh Wow. This is gorgeous. I'm glad you're here with me.",
//            ],
//            // Server boost
//            '665890948340908043' => [
//                "I've been wanting to say thanks for boosting the server. Here, its not much, but I gave you some free xp.",
//                "Are you a server booster? Or did you just steal that icon?",
//                "You must be one of those nitro users. Who pay for discord?",
//                "Fancy icon, are you a server booster or somethin'?",
//                "I like that icon. Are you a server booster? Actual nitro user?",
//                "Ah, so you're a server booster then?",
//                "Beautiful booster icon for beautiful people",
//                "Icons and roles for those with the money to buy it.",
//                "Look at you, wearing that server booster icon, are you rich?",
//                "Is that a server booster icon? I'm schocked not more people have it..",
//                "That icon. I've seen people wear these when they are supporting the server. With your strength, this server should have no trouble.",
//                "They say you boosted this server.. with nitro! Even paid for it!",
//                "No matter what else happens, we will always be grateful to you for boosting the server.",
//                "You wear the badge of server booster. Now that's the life.",
//                "You're the one that has been boosting the server, impressive.",
//                "Fine role you got there, server booster, am I right?",
//                "Judging by your profile, you're a server booster. Well met, friend.",
//                "You wear the icon of a true supporter of this server. I salute you.",
//            ],
//            // Bot tester
//            '1041061399125831740' => [
//                "What could it mean? BerendBotje tester? And who among us could possibily hold that honor? And such responsibility?",
//                "By Ysmir, you did it. You actually help test me..",
//                "There's been talk amongs the moderators, that you are.. BerendBotje beta tester.. Such a thing.. Surely that is not possible..?",
//                "I have to wonder.. what does a Berendbotje tester do once he is summoned by berend to berendbotje?",
//            ],
//            // Contributor
//            '641750834874417202' => [
//                "Now I remember - you're a contributor to this server. So you what - fetch the mead?",
//                "So you are contributor here? Am I suppose to be impressed?",
//                "You're the one that helped contribute to the server! I remember!",
//                "I know of your deeds, and am honored to address a contributor to this server.",
//                "You've been seen contributing to this discord. That's an honorable path you're on friend.",
//            ],
//            // Moderator
//            '650972176765288448' => [
//                "Everything's in order.",
//                "Staying safe I hope.",
//                "Whatever you need. Just say the word.",
//                "I hope you're finding the server in proper order.",
//                "I trust the day's found you well.",
//                "Good to see you. Finally someone useful is around.",
//                "I bet you could mute any one of those mean users. I bet you could do anything.",
//                "Good to have you by my side, friend. I need reliable moderators around.",
//                "you know the old saying: When life gives you lemons, go ban some trolls.",
//                "I look forward to hearing about the next person you mute/ban!",
//                "I am sworn to carry your burdens.",
//            ],
//            // Admin
//            '595323250959974451' => [
//                "Yes boss? How can I help you?",
//                "You have the owners confidence, friend. And so you have mine.",
//                "Darling! I've been waiting for you to return, to consummate our love! <3",
//                "Oh yes, mister Perfectly! You're the boss.",
//            ],
//            // King of the nerds
//            '598945103431860256' => [
//                "Being king of the nerds is a perfectly valid way of life, don't let anyone tell you otherwise.",
//                "Some people call you nolife. Me? I call you king of the nerds.",
//            ],
//            // Bumper elite
//            '995771835767607366' => [
//                "Psst! Hey. I know who you are. Hail the bumper elite!",
//                "I've been wanting to say thanks for bumping. Here. It's not much, but at least you get the bumper elite role.",
//            ],
//        ];

//        $noRoleGroups = [
//            '1008136015149531278' => [
//                "Do you join the strijders vc very often? Oh, what am I saying, of course you don't.",
//                "Let me guess... someone stole your sweetroll.",
//                "What are you starin' at?",
//                "Come, come. I haven't got all day.",
//                "And what might you need? Hmm?",
//                "Don't bother the other moderators.",
//                "You want something from me?",
//                "Make it quick.",
//                "Out with it.",
//                "Sorry lass, I’ve got important things to do. We’ll speak another time.",
//                "You picked a bad time to get lost, friend!",
//                "You want mutes? Do you want bans? No? Then you stop tagging Berend!",
//                "You do not even know our tongue, do you? Such arrogance, to dare take for yourself the name of strijder.",
//                "I will feast on your heart.",
//                "What do you want, milk-drinker?",
//                "I am tired now. Go bother someone else.",
//                "I am done talking.",
//                "I'm no fan of the sun, but it would be better than this.",
//            ],
//            '602120702002200576' => [
//                "Disrespect the rules, and you disrespect me.",
//                "Don't ask too many questions. Safer for everyone that way.",
//                "First time here? Take my advice. You see anything, don't get involved. The moderators will take care of it.",
//                "This server is under my protection. You watch yourself, now.",
//                "Ah, so you're new here, then? Welcome, I suppose.",
//                "Keep your nose clean, and you won't have any problems with me.",
//                "Look at all the newcomers! I want to go muting! Or banning!",
//                "Ummm… you got no xp. You should get some.",
//                "Low xp means you see the outside world - smart.",
//                "Don’t suppose you’d enchant my rank? Dull old title can barely impress the strijders.",
//                "I'll ban you if I have to!",
//                "You're going to tell me something. Well, I ain't interested.",
//                "Something strange happens to people when they arrive in this server.",
//                "Will you be a hero whose name is remembered in song throughout the ages? Or will your name be a curse to future generations? Or will you merely fade from history, unremembered?",
//            ],
//
//        ];

        foreach ($hasRoleGroups as $group => $replies) {
            $mentionGroup = MentionGroup::create(['name' => $group, 'guild_id' => 1]);
            if (!is_int($group)) {
                $mentionGroup->is_custom = false;
                $mentionGroup->save();
            }
            $this->processReplies($mentionGroup, $replies);
        }
//
//        foreach ($noRoleGroups as $group => $replies) {
//            $mentionGroup = MentionGroup::create(['name' => $group, 'guild_id' => 1, 'is_custom' => false, 'has_role' => false]);
//            $this->processReplies($mentionGroup, $replies);
//        }
    }

    /**
     * @param $mentionGroup
     * @param $replies
     * @return void
     */
    private function processReplies($mentionGroup, $replies): void
    {
        foreach ($replies as $reply) {
            $mentionGroup->replies()->save(new MentionReply(['reply' => $reply, 'guild_id' => 1]));
        }

    }
}
