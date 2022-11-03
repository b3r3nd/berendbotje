<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{

    protected $fillable = ['youtube_url', 'filename', 'queue_order', 'guid_id'];

}
