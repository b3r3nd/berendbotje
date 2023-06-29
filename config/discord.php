<?php

return [
    'token' => env('BOT_TOKEN', ' '),
    'urb-token' => env('URB_TOKEN', ' '),
    'urb-host' => env('URB_HOST', ' '),
    'open-ai-key' => env('OPENAI_API_KEY', ' '),
    'open-ai-host' => env('OPEN_AI_HOST', ' '),

    'modules' => [
        'bump' => env('ENABLE_BUMP', true),
        'channel-flags' => env('ENABLE_CHANNEL_FLAGS', true),
        'cringe' => env('ENABLE_CRINGE', true),
        'custom-commands' => env('ENABLE_CUSTOM_COMMANDS', true),
        'fun' => env('ENABLE_FUN', true),
        'help' => env('ENABLE_HELP', true),
        'levels' => env('ENABLE_LEVELS', true),
        'logger' => env('ENABLE_LOGGER', true),
        'mention' => env('ENABLE_MENTION_RESPONDER', true),
        'openai' => env('ENABLE_OPENAI', true),
        'reactions' => env('ENABLE_REACTIONS', true),
        'roles' => env('ENABLE_ROLES', true),
        'settings' => env('ENABLE_SETTINGS', true),
        'timeout' => env('ENABLE_TIMEOUT', true),
    ],
];
