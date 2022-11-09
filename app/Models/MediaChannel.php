<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaChannel extends Model
{

    protected $table = 'mediachannels';

    protected $fillable = ['channel', 'guild_id'];

    public static function byGuild($guildId)
    {
        return MediaChannel::where(['guild_id' => Guild::get($guildId)->id]);
    }
}
