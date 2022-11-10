<?php

namespace App\Discord\Core\Enums;

enum Setting: string
{
    case XP_COUNT = 'xp_count';
    case XP_COOLDOWN = 'xp_cooldown';
    case ENABLE_XP = 'enable_xp';

    case VOICE_XP_COUNT = 'xp_voice_count';
    case VOICE_XP_COOLDOWN = 'xp_voice_cooldown';
    case ENABLE_VOICE_XP = 'enable_voice_xp';

    case ENABLE_EMOTE = 'enable_emote_counter';
    case ENABLE_ROLE_REWARDS = 'enable_role_rewards';
    case ENABLE_BUMP = 'enable_bump_counter';
    case ENABLE_REACTIONS = 'enable_reactions';
    case ENABLE_COMMANDS = 'enable_commands';
}