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

    /**
     * @param string $id
     * @param int $level
     * @return bool
     */
    public static function hasLevel(string $id, int $level)
    {
        if($level == 0) { return true; }
        $admin = Admin::where(['discord_id' => $id])->first();
        if (!$admin) {
            return false;
        }
        return $admin->level >= $level;
    }

    /**
     * @param string $id
     * @param int $level
     * @return bool
     */
    public static function hasHigherLevel(string $id, int $level)
    {
        $admin = Admin::where(['discord_id' => $id])->first();
        if (!$admin) {
            return false;
        }
        return $admin->level > $level;
    }

}
