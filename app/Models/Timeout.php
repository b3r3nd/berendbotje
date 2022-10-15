<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeout extends Model
{
    protected $fillable = ['discord_id', 'discord_username', 'reason', 'length'];


}
