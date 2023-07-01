<?php

return [
    'token' => env('BOT_TOKEN', ' '),
    'urb-token' => env('URB_TOKEN', ' '),
    'urb-host' => env('URB_HOST', ' '),
    'open-ai-key' => env('OPENAI_API_KEY', ' '),
    'open-ai-host' => env('OPEN_AI_HOST', ' '),

    'modules' => [
        'fun' => env('ENABLE_FUN', true),
        'levels' => env('ENABLE_LEVELS', true),
        'logger' => env('ENABLE_LOGGER', true),
        'mention' => env('ENABLE_MENTION_RESPONDER', true),
        'roles' => env('ENABLE_ROLES', true),
        'settings' => env('ENABLE_SETTINGS', true),
        'moderation' => env('ENABLE_MOD', true),
        'help' => env('ENABLE_HELP', true),
    ],
];
