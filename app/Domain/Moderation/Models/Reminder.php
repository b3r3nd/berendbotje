<?php

namespace App\Domain\Moderation\Models;

use App\Domain\Discord\Guild;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = ['executed_at', 'interval', 'name', 'guild_id', 'channel', 'message'];

    protected $casts = [
        'executed_at' => 'datetime:Y-m-d',
    ];


    /**
     * @param $guildId
     * @return mixed
     */
    public static function byGuild($guildId): mixed
    {
        return self::where(['guild_id' => Guild::get($guildId)->id]);
    }
}
