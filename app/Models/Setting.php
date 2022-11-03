<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'guild_id'];

    public static function byGuild($guildId)
    {
        return Setting::where(['guild_id' =>  $guildId]);
    }
}
