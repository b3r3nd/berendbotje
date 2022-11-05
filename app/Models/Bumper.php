<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bumper extends Model
{
    protected $fillable = ['count', 'guild_id'];


    public static function byGuild($guildId)
    {
        return Bumper::where('guild_id', Guild::get($guildId)->id);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class);
    }
}


