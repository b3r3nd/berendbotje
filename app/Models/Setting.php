<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'guild_id'];


    public static function getSetting(string $key, string $guildId)
    {
        return Setting::where([
            ['guild_id', '=', Guild::get($guildId)->id],
            ['key', '=', $key],
        ])->first();
    }

    public static function hasSetting(string $key, string $guildId): bool
    {
        return !Setting::where([
            ['guild_id', '=', Guild::get($guildId)->id],
            ['key', '=', $key],
        ])->get()->isEmpty();
    }

    public static function byDiscordGuildId(string $guildId)
    {
        return Setting::where(['guild_id' => Guild::get($guildId)->id]);
    }
}
