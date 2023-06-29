<?php

namespace App\Discord\Settings\Enums;

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
    case ENABLE_LOGGING = 'enable_logging';
    case LOG_CHANNEL = 'log_channel_id';
    case ENABLE_BUMP_REMINDER = 'enable_bump_reminder';
    case BUMP_REMINDER_ROLE = 'bump_reminder_role';
    case BUMP_CHANNEL = 'bump_channel';
    case ENABLE_MENTION_RESPONDER = 'enable_mention_responder';
    case ENABLE_REMINDER = 'enable_qotd_reminder';
    case REMINDER_CHANNEL = 'qotd_channel';
    case REMINDER_ROLE = 'qotd_role';
}
