<?php

namespace App\Domain\Setting\Enums;

enum LogSetting: string
{
    case JOINED_SERVER = 'joined_server';
    case LEFT_SERVER = 'left_server';
    case KICKED_SERVER = 'kicked_from_server';
    case BANNED_SERVER = 'banned_from_server';
    case UNBANNED_SERVER = 'unbanned_from_server';
    case TIMEOUT = 'timeout';
    case JOINED_CALL = 'joined_call';
    case LEFT_CALL = 'left_call';
    case SWITCHED_CALL = 'switched_call';
    case MUTED_MOD_VOICE = 'muted_mod_voice';
    case UNMUTED_MOD_VOiCE = 'unmuted_mod_voice';
    case UPDATED_USERNAME = 'updated_username';
    case MESSAGE_UPDATED = 'message_updated';
    case MESSAGE_DELETED = 'message_deleted';
    case INVITE_CREATED = 'invite_created';
    case INVITE_REMOVED = 'invite_removed';
    case START_STREAM = 'start_stream';
    case END_STREAM = 'end_stream';
    case START_CAM = 'start_cam';
    case END_CAM = 'end_cam';
}
