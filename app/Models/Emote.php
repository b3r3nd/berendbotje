<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emote extends Model
{
    protected $table = 'emotes';
    protected $fillable = ['emote', 'count', 'hex', 'guild_id'];


    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public static function byGuild($guildId)
    {
        return Emote::where(['guild_id' =>  Guild::get($guildId)->id]);
    }

}
