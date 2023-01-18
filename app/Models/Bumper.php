<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bumper extends Model
{
    protected $fillable = ['count', 'guild_id'];


    /**
     * @param $guildId
     * @return Bumper
     */
    public static function byGuild($guildId)
    {
        return self::where('guild_id', Guild::get($guildId)->id);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class);
    }
}


