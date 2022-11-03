<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $fillable = [
        'trigger',
        'response',
        'guild_id',
    ];

    public static function byGuild($guildId)
    {
        return Command::where(['guild_id' =>  $guildId]);
    }
}
