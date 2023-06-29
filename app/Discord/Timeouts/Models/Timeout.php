<?php

namespace App\Discord\Timeouts\Models;

use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
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

    /**
     * @param $guildId
     * @return mixed
     */
    public static function byGuild($guildId): mixed
    {
        return self::where(['guild_id' => Guild::get($guildId)->id]);
    }

}
