<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CringeCounter extends Model
{

    protected $table = 'cringe_counter';

    protected $fillable = ['count'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class);
    }

    public static function byGuild($guildId)
    {
        return CringeCounter::whereHas('user', function (Builder $query) use ($guildId) {
            $query->where('guild_id', '=', $guildId);
        });
    }

}
