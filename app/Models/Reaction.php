<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    protected $fillable = ['trigger', 'reaction', 'guild_id'];

    /**
     * @return BelongsTo
     */
    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }

    /**
     * @param $guildId
     * @return mixed
     */
    public static function byGuild($guildId): mixed
    {
        return self::where(['guild_id' => Guild::get($guildId)->id]);
    }
}
