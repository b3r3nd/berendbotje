<?php

namespace App\Discord\Fun\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abuser extends Model
{
    use HasFactory;

    protected $table = 'abusers';
    protected $fillable = ['discord_id'];
}
