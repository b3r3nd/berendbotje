<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emote extends Model
{
    protected $table = 'emotes';
    protected $fillable = ['emote', 'count', 'hex', 'guild_id'];


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
        return Emote::where(['guild_id' =>  Guild::get($guildId)->id]);
    }

}
