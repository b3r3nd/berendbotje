<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Taalbestanden voor de Discord-bot
    |--------------------------------------------------------------------------
    |
    | De volgende taallijnen worden gebruikt in de bot als reacties op commando's
    | en op indexpagina's!
    */

    'lack-access' => 'Je hebt geen toestemming om dit commando uit te voeren. Gebruik `/role` om je rollen in deze server te controleren.',
    'error' => 'Fout',
    'done' => 'Succes',
    'media-deleted' => 'Je bericht in :channel is verwijderd. Alleen media en URLs zijn toegestaan. ',
    'no-valid-term' => 'Zoekterm :term kan niet worden gevonden.',
    'bump-reminder' => 'BUMP TIJD :role',
    'monthly' => 'Maandelijks',
    'all-time' => 'Altijd',
    'command' => 'Commando',
    'response' => 'Reactie',
    'trigger' => 'Trigger',
    'reaction' => 'Reactie',
    'user-mention' => 'Gebruikersvermelding',
    'question' => 'Vraag',
    'search-term' => 'Zoekterm',
    'cannot-count' => "Kan niet tellen :)",
    'wrong-number' => 'Verkeerd getal, reset naar :count',
    'level-up-msg' => 'Hey strijder <@:user>, je bent nu level :level, gefeliciteerd! Berend zal je nu op een drankje trakteren!',
    'level' => 'Level',
    'role' => 'Rol',
    'user' => 'Gebruiker',
    'key' => 'Sleutel',
    'group' => 'Groep',
    'value' => 'Waarde',
    'role-or-user-id' => 'Rol of Gebruikers ID',
    'role-or-user' => 'Is het een gebruiker of een rol?',
    'multiplier' => 'Gebruiksmultiplier',
    'group-id' => 'Groep - ID',
    'reply' => 'Antwoord',
    'reply-id' => 'Antwoord - ID',
    'reason' => 'Reden',
    'channel' => 'Kanaal',
    'flags' => 'Vlaggen',
    'permission' => 'Toestemming',
    'date - range' => 'Datumreeks',

    'permissions - enum' => [
        'roles' => 'Rollen',
        'create-role' => 'Rol aanmaken',
        'delete-role' => 'Rol verwijderen',
        'update-role' => 'Rol bijwerken',
        'permissions' => 'Toestemmingen',
        'attach-permission' => 'Toestemming bijwerken voor rol',
        'attach-role' => 'Rollen bijwerken voor gebruiker',
        'config' => 'Configuratie bekijken en bijwerken',
        'timeouts' => 'Time - outhistorie bekijken',
        'add-cringe' => 'De cringe - teller verhogen',
        'delete-cringe' => 'De cringe - teller verlagen',
        'commands' => "Aangepaste commando's maken en verwijderen",
        'reactions' => 'Aangepaste reacties maken en verwijderen',
        'role-rewards' => 'Rollenbeloningen maken en bijwerken',
        'manage-xp' => 'XP aanpassenaan gebruikers',
        'channels' => 'Kanaalvlaggen instellen en bijwerken',
        'logs' => 'De logconfiguratie bijwerken',
        'add-mention' => 'Nieuwe vermeldingsreacties toevoegen',
        'delete-mention' => 'Mentions verwijderen',
        'manage-mention-groups' => 'Mention-reactiegroepen beheren',
        'media-filter' => 'Mediasfilter',
        'openai' => "'OpenAi-commando's gebruiken",
        'abusers' => 'De zwarte lijst beheren',
    ],

    'slash' => [
        'userset' => 'Gebruikersspecifieke instellingen bijwerken voor deze server',
        'userconfig' => 'Gebruikersspecifieke instellingen voor deze server',
        'blacklist' => 'De gebruikerszwarte lijst weergeven',
        'block' => 'Gebruiker toevoegen aan de zwarte lijst',
        'unblock' => 'Gebruiker verwijderen uit de zwarte lijst',
        'roles' => 'Alle rollen in de server weergeven',
        'users' => 'Alle gebruikers weergeven met hun rollen',
        'permissions' => 'Alle beschikbare toestemmingen weergeven',
        'servers' => 'Alle servers weergeven waarop de bot actief is',
        'myroles' => 'Je rollen in deze server weergeven',
        'userroles' => 'De rollen van jezelf of een gegeven gebruiker weergeven',
        'delete-role' => 'Een rol verwijderen uit de server',
        'create-role' => 'Een nieuwe rol aan de server toevoegen',
        'detach-role-perm' => 'Toestemmingen verwijderen van rol',
        'attach-role-perm' => 'Toestemmingen toevoegen aan rol',
        'attach-user-role' => 'Gebruiker aan rol toevoegen',
        'detach-user-role' => 'Gebruiker uit rol verwijderen',
        'set' => 'Instelling bijwerken vanuit configuratie',
        'config' => 'De serverconfiguratie weergeven',
        'user-timeouts' => 'Time - outs weergeven voor individuele gebruiker',
        'timeouts' => 'Alle time - outs weergeven of per individuele gebruiker',
        'modstats' => 'Moderatorstatistieken weergeven',
        'leaderboard' => 'Leaderboard met gebruikersniveaus weergeven',
        'rank' => 'Je eigen niveau en XP weergeven',
        'rewards' => 'Rollenbeloningen weergeven op basis van niveaus',
        'add-role-reward' => 'Rollenbeloning toevoegen aan een niveau',
        'del-role-reward' => 'Rollenbeloningen verwijderen van een niveau',
        'give-xp' => 'XP geven aan een gebruiker',
        'remove-xp' => 'XP verwijderen van een gebruiker',
        'reset-xp' => 'XP resetten voor een gebruiker',
        'cringecounter' => 'Weergeven wie het meest cringy is...',
        'inc-cringe' => 'De cringe - teller met één verhogen voor iemand',
        'dec-cringe' => 'De cringe - teller met één verlagen voor iemand',
        'reset-cringeDe cringe - teller resetten voor iemand',
        'bumpstats' => 'Elite bump - statistieken weergeven',
        'emotes' => 'Emote - teller weergeven',
        'commands' => "Lijst van aangepaste commando's weergeven",
        'reactions' => 'Lijst van aangepaste reacties weergeven',
        'add-command' => 'Een nieuw aangepast commando toevoegen',
        'del-command' => 'Een aangepast commando verwijderen',
        'add-reaction' => 'Een nieuwe aangepaste reactie toevoegen',
        'del-reaction' => 'Een aangepaste reactie verwijderen',
        '8ball' => 'De magische 8-ball om advies vragen',
        'ask' => 'Een ja- of nee-vraag stellen',
        'urb' => 'Zoeken in de Urban Dictionary',
        'help' => 'Helpbestanden met optionele parameters',
        'channels' => 'Overzicht van alle kanalen en hun vlaggen',
        'mark-channel' => 'Vlaggen toevoegen aan een kanaal',
        'unmark-channel' => 'Vlaggen verwijderen van een kanaal',
        'logconfig' => 'Configuratie voor logkanaal',
        'logset' => 'Delen van het logboek in- of uitschakelen',
        'mentionindex' => 'Lijst van alle antwoorden die worden gebruikt door de mention-responder',
        'addgroup ' => 'Een nieuwe vermeldingsgroep maken',
        'delgroup' => 'Een vermeldingsgroep verwijderen',
        'updategroup' => 'Een vermeldingsgroep bijwerken',
        'addreply' => 'Een nieuw antwoord toevoegen aan een vermeldingsgroep',
        'delreply' => 'Een antwoord verwijderen uit een vermeldingsgroep',
        'searchreply ' => 'Zoeken naar een antwoord',
        'mentiongroups' => 'Mention-respondergroep',
    ],

    'userconfig' => [
        'title' => 'Gebruikersinstellingen',
        'not-found' => 'Geen gebruikersinstellingen gevonden, zie `/help` -> gebruikersinstellingen voor meer informatie.',
    ],

    'blacklist' => [
        'title' => 'Gebruikerszwarte lijst',
        'block' => ":user toegevoegd aan de zwarte lijst.",
        'unblock' => ":user is verwijderd uit de zwarte lijst.",
        'blocked' => ':user staat al op de zwarte lijst.',
        'unblocked' => ':user staat niet op de zwarte lijst.'
    ],

    'channels' => [
        'added' => 'Kanaalvlag :flag toegevoegd aan kanaal <#:channel>.',
        'deleted' => 'Kanaalvlag :flag verwijderd van kanaal <#:channel>.',
        'has-flag' => 'Kanaal :channel heeft al die vlag.',
        'no-flag' => 'Kanaal :channel heeft die vlag niet.',
        'no-channel' => 'Geef een geldig kanaal op.',
        'title' => 'Kanaalvlaggen',
        'description' => "Kanaalvlaggen met hun vlaggen,zie `/help` voor meer informatie over wat elke vlag betekent.\n\n:channels"
    ],

    'logset' => [
        'updated' => 'Loginstelling :key bijgewerkt.',
        'title' => 'Loginstellingen',
    ],


    'userroles' => [
        'title' => 'Rollen in deze server',
        'description' => "Rollen voor :user \n\n :roles",
        'none' => ':user heeft geen rollen in deze server.',
    ],

    'users' => [
        'title' => 'Gebruikers voor deze server',
    ],

    'roles' => [
        'title' => 'Rollen voor deze server',
        'description' => ':roles',
        'exist' => 'Rol bestaat al',
        'created' => 'Rol :role aangemaakt.',
        'not-exist' => 'Rol :role bestaat niet.',
        'deleted' => 'Rol :role verwijderd.',
        'perm-attached' => 'Toestemming :perm toegekend aan rol :role.',
        'role-attached' => 'Rol :role toegekend aan gebruiker :user.',
        'perm-detached' => 'Toestemming :perm verwijderd van rol :role.',
        'role-detached' => 'Rol :role verwijderd van gebruiker :user.',
        'has-users' => 'Je kunt geen rollen verwijderen die door gebruikers worden gebruikt. Verwijder eerst de gebruikers.',
        'admin-role' => 'Kan de beheerdersrol niet verwijderen.',
        'admin-role-perms' => 'Je kunt geen machtigingen verwijderen van de hoofdbeheerdersrol.',
        'admin-role-owner' => 'Je kunt de eigenaar niet verwijderen uit de lijst met beheerders.',
    ],
    'permissions' => [
        'title' => 'Algemene toestemmingen',
        'description' => ':perms',
        'not-exist' => 'Toestemming :perm bestaat niet.',
    ],

    '8ball' => [
        'no-question' => 'Je moet me een vraag stellen..',
    ],

    'rewards' => [
        'title' => 'Rollenbeloningen',
        'description' => "Level • Rolbeloning\n\n:rewards",
        'added' => 'Rollenbeloning :role toegevoegd voor level :level.',
        'deleted' => 'Alle rollenbeloningen voor level :level verwijderd.',
        'number' => 'Zowel het level als de rol-ID moeten numeriek zijn.'
    ],

    'xp' => [
        'not-found' => ':user heeft geen berichten.',
        'count' => 'Je hebt :messages.',
        'title' => 'Level :level',
        'description' => "Gebruiker: :user\nLevel: :level \nXP: :xp\nBerichten: :messages \nVoice: :voice",
        'given' => ':xp xp gegeven aan <@:user>.',
        'removed' => ':xp xp verwijderd van <@:user>.',
        'reset' => 'xp voor <@:user> is gereset.',
        'not-exist' => 'Gebruiker <@:user> heeft geen berichten of ervaring in deze server.'
    ],

    'set' => [
        'title' => 'Algemene botinstellingen',
        'not-exist' => 'Instelling :key bestaat niet.',
        'updated' => 'Instelling :key is bijgewerkt naar waarde :value.',
        'not-numeric' => 'Instellingswaarden moeten numeriek zijn, :value is geen numerieke waarde.',
    ],

    'messages' => [
        'title' => 'XL Leaderboard',
        'description' => "Lijst van berichten en xp voor gebruikers.\n\n:users",
    ],

    'buttons' => [
        'next' => 'Volgende pagina',
        'previous' => 'Vorige pagina'
    ],

    'adminstats' => [
        'title' => 'Moderatorstatistieken',
        'description' => "Wie heeft de macht?\n\n",
    ],

    'bump' => [
        'inc' => ":name heeft deze Discord :count keer gebumpt!",
        'title' => 'Bump Elites',
        'description' => "Bump-tellers aller tijden!\n\n:bumpers",
        'description-month' => "Bump-tellers van deze maand!\n\n:bumpers",
    ],

    'cringe' => [
        'title' => 'Cringe-teller',
        'description' => "Lijstvan de meest cringe mensen in onze Discord! \n\n:users",
        'count' => "Cringe-teller voor :name is :count.",
        'change' => "Cringe-teller voor :name is nu :count.",
        'not-cringe' => ":name is niet cringe.",
        'reset' => "Cringe-teller voor :user is gereset naar 0.",
        'fail' => "Mooie poging, noob, maar ik heb in plaats daarvan je cringe-teller verhoogd. De teller staat nu op :count."
    ],

    'cmd' => [
        'saved' => 'Commando :trigger opgeslagen met antwoord :response.',
        'deleted' => 'Commando :trigger verwijderd.',
        'title' => 'Commando\'s',
        'description' => "Basis tekstcommando's. \n\n :cmds",
    ],

    'reactions' => [
        'saved' => 'Reactie :reaction op :name opgeslagen.',
        'deleted' => 'Reactie voor :name verwijderd.',
        'title' => 'Reacties',
        'description' => "Basisreacties.",
    ],

    'timeout' => [
        'title' => 'Time-outs',
        'count' => "Totaal aantal time-outs: :count.",
    ],

    'emotes' => [
        'title' => 'Emote-teller',
        'description' => "Lijst van meest gebruikte emotes.\n\n:emotes",
    ],

    'help' => [
        'title' => 'Help',
        'footer' => 'Schakel tussen onderstaande secties.',
    ],

    'mention' => [
        'title' => 'Vermeldingsreacties',
        'description' => ':data',
        'added' => "**Antwoord:** \n :reply \n\n **Groep:** :group",
        'deleted' => 'Antwoord is verwijderd.',
        'no-group' => 'Groep niet gevonden, gebruik het groeps-ID.',
        'no-reply' => 'Antwoord niet gevonden, gebruik het antwoord-ID.',
    ],
    'mentiongroup' => [
        'title' => 'Vermeldingsgroepen',
        'description' => ':data',
        'added' => 'Vermeldingsgroep :group toegevoegd.',
        'deleted' => 'Vermeldingsgroep en al zijn antwoorden verwijderd.',
        'not-found' => 'Groep met ID :ID niet gevonden.',
        'integer' => 'Een groep moet het ID van een serverrol zijn! (voor nu).',
        'notexist' => 'Geen vermeldingsgroep gevonden voor ID :group.',
        'updated' => 'Vermeldingsgroep bijgewerkt.',
    ],
    'log' => [
        'no-dm' => 'Slash-commandos werken niet in privéberichten.',
        'failed' => 'Kon :trigger niet gebruiken, ontbreekt toestemming.',
        'success' => ':trigger gebruikt.',
        'joined' => ' <@:user> is lid geworden van de server',
        'kicked' => ' <@:user> is uit de server gekickt',
        'banned' => ' <@:user> is verbannen van de server',
        'left' => ' <@:user> heeft de server verlaten',
        'unbanned' => ' <@:user> is niet langer verbannen van de server',
        'username-change' => "**Gebruikersnaam gewijzigd** \n\n **Van**\n:from\n\n**Naar**\n:to\n",
        'create-invite' => ' <@:inviter> heeft een nieuwe uitnodigingslink aangemaakt',
        'remove-invite' => 'Uitnodigingslink van <@:inviter> verwijderd',
        'send-dm' => "Stuurt een DM:\n\n :content",
        'update-msg' => "<@:user> heeft een bericht bijgewerkt in <#:channel>\n\n**Oud Bericht**\n:old\n\n**Nieuw Bericht**\n:new",
        'delete-msg' => "<@:user> heeft zijn bericht verwijderd in <#:channel>\n\n**Bericht**\n:message",
        'timeout' => ' < @:user> heeft een time - out ontvangen',
        'joined-call' => "<@:user> is lid geworden van <#:channel>",
        'left-call' => "<@:user> heeft <#:channel> verlaten",
        'switch-call' => ' <@:user> is overgestapt van <#:oldchannel> naar <#:newchannel>',
        'muted-call' => '<@:user> is gedempt in de spraakchat',
        'unmuted-call' => '<@:user> is gedempt in de spraakchat opgeheven',
        'start-stream' => '<@:user> is begonnen met streamen in <#:channel>',
        'stop-stream' => '<@:user> is gestopt met streamen in <#:channel>',
        'enable-cam' => '<@:user> heeft zijn webcam ingeschakeld in <#:channel>',
        'disable-cam' => '<@:user> heeft zijn webcam uitgeschakeld in <#:channel>',
    ],
];

