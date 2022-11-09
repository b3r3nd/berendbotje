<?php

namespace App\Discord\Core;

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

    case ROLE_REWARDS = 'role-rewards';

    // Bot Owner permissions not available in public servers
    case ADMIN_SERVER = 'servers';
    case ADMINS = 'admins';

}
