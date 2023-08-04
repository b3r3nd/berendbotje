<?php

namespace App\Domain\Moderation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abuser extends Model
{
    use HasFactory;

    protected $table = 'abusers';
    protected $fillable = ['discord_id', 'guild_id', 'reason'];


    /**
     * @param string $guildId
     * @return mixed
     */
    public static function byDiscordGuildId(string $guildId): mixed
    {
        return self::where('guild_id', $guildId);
    }
}
