<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['channel_id', 'guild_id', 'no_xp', 'media_only'];

    protected $casts = ['no_xp' => 'boolean', 'media_only' => 'boolean'];

    /**
     * @param string $channelId
     * @param string $guildId
     * @return mixed
     */
    public static function get(string $channelId, string $guildId): mixed
    {
        return Channel::where([
            ['guild_id', '=', Guild::get($guildId)->id],
            ['channel_id', '=', $channelId],
        ])->first();
    }

    /**
     * @param $guildId
     * @return mixed
     */
    public static function byGuild($guildId): mixed
    {
        return Channel::where('guild_id', Guild::get($guildId)->id);
    }

}
