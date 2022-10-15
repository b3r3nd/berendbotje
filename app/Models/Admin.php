<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'discord_admins';

    protected $fillable = [
        'discord_id',
        'discord_username',
        'level'
    ];

    /**
     * @param string $id
     * @return bool
     */
    public static function isAdmin(string $id)
    {
        return !Admin::where(['discord_id' => $id])->get()->isEmpty();
    }
}
