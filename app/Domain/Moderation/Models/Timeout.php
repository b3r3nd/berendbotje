<?php

namespace App\Domain\Moderation\Models;

use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
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
        return $this->belongsTo(User::class);
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
