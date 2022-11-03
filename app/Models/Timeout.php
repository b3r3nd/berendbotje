<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timeout extends Model
{
    protected $fillable = ['discord_id', 'discord_username', 'reason', 'length', 'giver_id', 'guild_id'];


    /**
     * @return BelongsTo
     */
    public function giver(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class);
    }

    public static function byGuild($guildId)
    {
        return Timeout::where(['guild_id' =>  $guildId]);
    }

}
