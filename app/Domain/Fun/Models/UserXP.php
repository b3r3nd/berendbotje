<?php

namespace App\Domain\Fun\Models;

use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
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
        return $this->belongsTo(User::class);
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
