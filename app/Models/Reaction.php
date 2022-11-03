<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = ['trigger', 'reaction', 'guild_id'];

    public static function byGuild($guildId)
    {
        return Reaction::where(['guild_id' =>  $guildId]);
    }
}
