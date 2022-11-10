<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageCounter extends Model
{
    protected $table = 'message_counter';

    protected $fillable = ['count', 'voice_seconds', 'guild_id', 'xp', 'level'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class);
    }

    public static function byGuild($guildId)
    {
        return MessageCounter::where('guild_id', Guild::get($guildId)->id);
    }

}
