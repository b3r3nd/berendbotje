<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emote extends Model
{
    protected $table = 'emotes';
    protected $fillable = ['emote', 'count', 'hex'];
}
