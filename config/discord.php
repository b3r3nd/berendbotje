<?php

return [
    'token' => env('BOT_TOKEN', ' '),
    'urb-token' => env('URB_TOKEN', ' '),
    'urb-host' => env('URB_HOST', ' '),
    'open-ai-key' => env('OPENAI_API_KEY', ' '),
    'open-ai-host' => env('OPEN_AI_HOST', ' '),

    'modules' => [
        'roles' => env('ENABLE_ROLES', true),
        'xp' => env('ENABLE_LEVELS', true),
        'mods' => env('ENABLE_MODS', true),
        'fun' => env('ENABLE_FUN', true),
        'mention' => env('ENABLE_MENTION', true),
        'openai' => env('ENABLE_OPEN_AI', true),
        'settings' => env('ENABLE_SETTINGS', true),
    ],
];
