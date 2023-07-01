<?php

namespace App\Discord\Moderation\Models;

use App\Discord\Core\Models\Guild;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['channel_id', 'guild_id', 'no_xp', 'media_only', 'no_stickers', 'no_log'];

    protected $casts = ['no_xp' => 'boolean', 'media_only' => 'boolean', 'no_stickers' => 'boolean', 'no_log' => 'boolean'];

    /**
     * @param string $channelId
     * @param string $guildId
     * @return ?Channel
     */
    public static function get(string $channelId, string $guildId): ?Channel
    {
        return self::where([
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
        return self::where('guild_id', Guild::get($guildId)->id);
    }

}
