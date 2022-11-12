<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSetting extends Model
{
    protected $table = 'log_settings';
    protected $fillable = ['guild_id', 'key', 'value'];
    protected $casts = ['value' => 'boolean'];

    /**
     * @param string $key
     * @param string $guildId
     * @return mixed
     */
    public static function getSetting(string $key, string $guildId): mixed
    {
        return LogSetting::where([
            ['guild_id', '=', Guild::get($guildId)->id],
            ['key', '=', $key],
        ])->first();
    }

    /**
     * @param string $key
     * @param string $guildId
     * @return bool
     */
    public static function hasSetting(string $key, string $guildId): bool
    {
        return !LogSetting::where([
            ['guild_id', '=', Guild::get($guildId)->id],
            ['key', '=', $key],
        ])->get()->isEmpty();
    }

    /**
     * @param string $guildId
     * @return mixed
     */
    public static function byDiscordGuildId(string $guildId): mixed
    {
        return LogSetting::where(['guild_id' => Guild::get($guildId)->id]);
    }
}
