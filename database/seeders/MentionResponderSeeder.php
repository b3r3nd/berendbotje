<?php

namespace Database\Seeders;

use App\Models\MentionGroup;
use App\Models\MentionReply;
use Illuminate\Database\Seeder;

class MentionResponderSeeder extends Seeder
{

    /**
     * @return void
     * LOW LEVEL
     * "Ummm… you got no xp. You should get some."
     *
     *
     *
     *
     *
     *
     *
     * NON STRIJDER
     *
     *
     *
     *
     *
     *
     *
     *
     */

    public function run()
    {

        $hasRoleGroups = [
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
                "Timeout one person, and you can solve so many problems. I wonder at the possibilities.",
                "In their tongue he is Berend - de kale.",
                "Perhaps we should find a random stranger to ban. Practice does make perfect.",
                "BERENDD!!!",
                "Lots of history in these chats. We’re trying to make some more. It’s a lucky time to be alive.",
                "And he says to the man, ‘That’s not a horker! That’s Koen!",
                "All the living shall fear the mods.",
                "Ho ho ho and hee hee hee, break that mute across me knee. And if the mod should choose to fight, why then I set his clothes alight!",
                "The users I serve aren’t exactly what I’d call an admin’s feast, but they’ll fill your time.",
                "Some people call him Berend. Me? I call him often.",
                "Berendbotje knows much, tells some. Berendbotje knows many things others do not.",
                "Those admins from Discord? They’ve got ban hammers. Ban. Hammers.",
                "You know what's wrong with the Netherlands these days? Everyone is obsessed with Berend and not me.",
                "I saw a weeb the other day. Horrible creatures.",
                "for Berend!",
                "You know what we call people who mess with us? Banned.",
                "...What? Berend? Is that your voice I hear? Hmm... No, no... Just my head playing tricks... Foolish me...",
                "I can talk for a moment. Just a moment. Then Berend must be... tended to.",
                "I share stories and rumours with my moderators, It's fun most days but hard work",
                "I always wondered what Berend actually looked like. I hear he's like humans, but without hair.",
                "Don't try blocking if you have unwanted messages. You will only get confused. Much better to send a  moderator anyway.",
                "I'd read stories about Berend, but I didn't expect him to be that big!",
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
            // Strijder
            '1008136015149531278' => [
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
                "I’ve never seen anything quite like you.",
                "Yes, strijder? How can I be of service?",
                "Oh, of course. a strijder needs me, after all."
            ],
            // Weeb
            '921387543268831273' => [
                "Weebs are not welcome in the general chat, so they make their camps in the #weeb channel.",
                "Weebs have no business in this chat, outlander.",
                "And why should I speak with you? You are a weeb.",
                "These Weebs don’t even have the decency to dress properly.",
                "Enough! I will not stand idly by while a Weeb runs wild and infects my people!",
                "You have opened the door to darkness, little man.",
                "I can be trusted. I know this. But they do not. Onikaan ni ov weebs. It is always wise to mistrust a weeb.",
                "You'll make a fine rug, Weeb!",
                "I'll show you to your corner. Right this way.",
                "You should have stayed in your filthy channel, weeb!",
                "I was always taught to avoid these types of people. I think I see why, now.",
                "Weebs... an unclean lot. It will be a pleasure to clear this place.",
            ],
            // NSFW
            '985997740356018257' => [
                "You should be embarrassed to have that role... its not safe (for work).",
                "Please. Remove your naked obscenity from this discord of civilized folk.",
                "Enough already. Go back to NSFW. You're making people nervous.",
                "You must either be crazy or supremely confident to have the NSFW role without shame.",
                "NSFW.. Not my thing, but who am I to judge...",
                "Oh, dear. Would you look at that? NSFW? How embarrassing.",
                "NSFW role huh? Was it your ma or pa that left you?",
                "Keep your hands to yourself, pervert.",
                "Either I’m drunk, or you’re naked. Possibly both.",
            ],
            // Legendary
            '602121042730680339' => [
                "These sands are cold, but I feel warmness from your presence.",
                "May the gods watch over your high rank on this server, friend.",
                "Well, look at you. If only everyone was as active in the server as you.",
                "Good to see this server still has such fine and active people. You give me hope.",
                "I hope your parents are proud of you. Although looking at how much XP you got in this server.. I got my doubts",
                "May your next rank be the king of the nerds, dethrone rickert!",
                "Good to see you. At least you know how to use this discord properly.",
                "Wow. Nice rank! Can I have it? I promise I give it back. Honest!",
                "So you're interested in becoming king of the nerds?",
                "You've shown yourself mighty, both in Voice and messages. In order to defeat the admins, you've gained mastery of dreadful weapons. Now it is up to you to decide what to do with your power and skill. Your future lies before you.",
                "Oh Wow. This is gorgeous. I'm glad you're here with me.",
            ],
            // Server boost
            '665890948340908043' => [
                'Nog niks',
            ],
            // Moderator
            '650972176765288448' => [
                "Everything's in order.",
                "Staying safe I hope.",
                "Whatever you need. Just say the word.",
                "I hope you're finding the server in proper order.",
                "I trust the day's found you well.",
                "Good to see you. Finally someone useful is around.",
                "I bet you could mute any one of those mean users. I bet you could do anything.",
                "Good to have you by my side, friend. I need reliable moderators around.",
                "you know the old saying: When life gives you lemons, go ban some trolls.",
                "I look forward to hearing about the next person you mute/ban!",
                "I am sworn to carry your burdens",
            ],
            // Admin
            '595323250959974451' => [
                "Yes boss? How can I help you?",
                "You have the owners confidence, friend. And so you have mine.",
                "Darling! I've been waiting for you to return, to consummate our love! <3",
                "Oh yes, mister Perfectly! You're the boss.",
            ],
            // King of the nerds
            '598945103431860256' => [
                "Being king of the nerds is a perfectly valid way of life, don't let anyone tell you otherwise.",
                "Some people call you nolife. Me? I call you king of the nerds.",
            ],
            // Bumper elite
            '995771835767607366' => [
                "Psst! Hey. I know who you are. Hail the bumper elite!",
                "I've been wanting to say thanks for bumping. Here. It's not much, but at least you get the bumper elite role.",
            ],
        ];

        $noRoleGroups = [
            '1008136015149531278' => [
                "Do you join the strijders vc very often? Oh, what am I saying, of course you don't.",
                "Let me guess... someone stole your sweetroll.",
                "What are you starin' at?",
                "Come, come. I haven't got all day.",
                "And what might you need? Hmm?",
                "Don't bother the other moderators.",
                "You want something from me?",
                "Make it quick.",
                "Out with it.",
                "Sorry lass, I’ve got important things to do. We’ll speak another time.",
                "You picked a bad time to get lost, friend!",
                "You want mutes? Do you want bans? No? Then you stop tagging Berend!",
                "You do not even know our tongue, do you? Such arrogance, to dare take for yourself the name of strijder.",
                "I will feast on your heart.",
                "What do you want, milk-drinker?",
                "I am tired now. Go bother someone else.",
                "I am done talking.",
                "I'm no fan of the sun, but it would be better than this.",
            ],
            '602120702002200576' => [
                "Disrespect the rules, and you disrespect me.",
                "Don't ask too many questions. Safer for everyone that way.",
                "First time here? Take my advice. You see anything, don't get involved. The moderators will take care of it.",
                "This server is under my protection. You watch yourself, now.",
                "Ah, so you're new here, then? Welcome, I suppose.",
                "Keep your nose clean, and you won't have any problems with me.",
                "Look at all the newcomers! I want to go muting! Or banning!",
                "Ummm… you got no xp. You should get some.",
                "Low xp means you see the outside world - smart.",
                "Don’t suppose you’d enchant my rank? Dull old title can barely impress the strijders.",
                "I'll ban you if I have to!",
                "You're going to tell me something. Well, I ain't interested.",
                "Something strange happens to people when they arrive in this server.",
                "Will you be a hero whose name is remembered in song throughout the ages? Or will your name be a curse to future generations? Or will you merely fade from history, unremembered?",
            ],

        ];

        foreach ($hasRoleGroups as $group => $replies) {
            $mentionGroup = MentionGroup::create(['name' => $group, 'guild_id' => 1]);
            if (!is_int($group)) {
                $mentionGroup->is_custom = false;
                $mentionGroup->save();
            }
            $this->processReplies($mentionGroup, $replies);
        }

        foreach ($noRoleGroups as $group => $replies) {
            $mentionGroup = MentionGroup::create(['name' => $group, 'guild_id' => 1, 'is_custom' => false, 'has_role' => false]);
            $this->processReplies($mentionGroup, $replies);


        }
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
