<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'discord_admins';

    protected $fillable = [
        'level',
        'user_id',
    ];

    public static function byGuild($guildId)
    {
        return self::whereHas('user', function (Builder $query) use ($guildId) {
            $query->where('guild_id', '=', $guildId);
        });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class);
    }
}
