<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentionReply extends Model
{
    use HasFactory;


    protected $fillable = ['reply', 'guild_id'];

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(MentionGroup::class);
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
