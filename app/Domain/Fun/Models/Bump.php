<?php

namespace App\Domain\Fun\Models;

use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bump extends Model
{
    protected $table = 'bumpers';
    protected $fillable = ['count', 'guild_id'];


    /**
     * @param $guildId
     * @return Bump
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
        return $this->belongsTo(User::class);
    }
}


