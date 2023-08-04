<?php

namespace App\Domain\Fun\Models;

use App\Domain\Discord\Guild;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Command extends Model
{
    protected $fillable = [
        'trigger',
        'response',
        'guild_id',
    ];

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
