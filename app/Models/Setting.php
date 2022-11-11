<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'guild_id'];

    /**
     * @param string $key
     * @param string $guildId
     * @return mixed
     */
    public static function getSetting(string $key, string $guildId): mixed
    {
        return Setting::where([
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
        return !Setting::where([
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
        return Setting::where(['guild_id' => Guild::get($guildId)->id]);
    }
}
