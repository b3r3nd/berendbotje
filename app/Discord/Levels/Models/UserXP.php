<?php

namespace App\Discord\Levels\Models;

use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserXP extends Model
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

    /**
     * @param $guildId
     * @return mixed
     */
    public static function byGuild($guildId): mixed
    {
        return self::where('guild_id', Guild::get($guildId)->id);
    }

}
