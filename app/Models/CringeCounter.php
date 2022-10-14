<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CringeCounter extends Model
{

    protected $table = 'cringe_counter';

    protected $fillable = ['discord_id', 'discord_tag', 'discord_username', 'count'];

}
