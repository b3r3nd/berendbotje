<?php

namespace App\Discord\Roles\Enums;

/**
 * Permissions are hardcoded in our permission seeder, they might change, or get updated. Use this ENUM instead of
 * hardcoded string names to prevent issues in the future.
 */
enum Permission: string
{
    case NONE = "";
    case ROLES = 'roles';
    case CREATE_ROLE = 'create-role';
    case DELETE_ROLE = 'delete-role';
    case UPDATE_ROLE = 'update-role';
    case PERMISSIONS = 'permissions';
    case ATTACH_PERM = 'attach-permission';
    case ATTACH_ROLE = 'attach-role';
    case CONFIG = 'config';
    case TIMEOUTS = 'timeouts';
    case MEDIA = 'media-filter';
    case ADD_CRINGE = 'add-cringe';
    case DEL_CRINGE = 'delete-cringe';
    case COMMANDS = 'commands';
    case REACTIONS = 'reactions';
    case CHANNEL = 'channels';
    case LOGS = 'logs';
    case ROLE_REWARDS = 'role-rewards';
    case MANAGE_XP = 'manage-xp';
    case ADD_MENTION = 'add-mention';
    case DEL_MENTION = 'delete-mention';
    case MANAGE_MENTION_GROUP = 'manage-mention-groups';
    case OPENAI = 'openai';
    case ABUSERS = 'abusers';

    // Bot Owner permissions not available in public servers
    case ADMIN_SERVER = 'servers';
    case ADMINS = 'admins';

}
