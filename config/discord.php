<?php

use App\Discord\Core\Providers\CommandServiceProvider;
use App\Discord\Core\Providers\EventServiceProvider;
use App\Discord\Core\Providers\GuildServiceProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Bot Token
    |--------------------------------------------------------------------------
    |
    | This is your bot token you can see only once when creating your bot
    | in the discord developers portal. KEEP IT SECRET!
    |
    */

    'token' => env('BOT_TOKEN', ' '),

    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    |
    | The application ID is the ID of your bot, it can be found in the dev
    | portal at the top!
    |
    */

    'app-id' => env('APPLICATION_ID', ' '),

    /*
    |--------------------------------------------------------------------------
    | Discord API
    |--------------------------------------------------------------------------
    |
    | We use this API directly to manage our slash commands, instead of doing
    | it through the bot.
    |
    */

    'api' => env('DISCORD_API', 'https://discord.com/api/v10/'),

    /*
    |--------------------------------------------------------------------------
    | Support Guild
    |--------------------------------------------------------------------------
    |
    | ID of your main guild, in this guild any administrative commands
    |
    */

    'support-guild' => env('SUPPORT_GUILD', ' '),

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Service providers can be used to "provide a server", basically load
    | and setup some stuff the bot needs. The Event en Command Service
    | Provider are default, I added one for guilds since I need to
    | load all the guilds on boot.
    |
    | Implement the ServiceProvider interface!
    |
    */
    'providers' => [
        EventServiceProvider::class,
        CommandServiceProvider::class,
        GuildServiceProvider::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | Top.gg
    |--------------------------------------------------------------------------
    |
    | If you have your bot listed on top.gg you can use their api to update
    | the server count on the website. So people know your bot is being
    | used!
    |
    */

    'topgg-host' => env('TOPGG_HOST', 'https://top.gg/api/'),
    'topgg-id' => env('TOPGG_ID', ' '),
    'topgg-token' => env('TOPGG_TOKEN', ' '),


    /*
    |--------------------------------------------------------------------------
    | Other Settings
    |--------------------------------------------------------------------------
    |
    | My bot uses Urban Dictionary and OpenAI API's for some commands, when
    | you create settings in your bot. Add them to your .env, add them
    | here and use this config to access the values.
    |
    */

    'urb-token' => env('URB_TOKEN', ' '),
    'urb-host' => env('URB_HOST', ' '),
    'open-ai-key' => env('OPENAI_API_KEY', ' '),
    'open-ai-host' => env('OPEN_AI_HOST', ' '),

];
