<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiscordUser extends Model
{
    protected $table = 'discord_users';
    protected $fillable = ['discord_id', 'discord_tag', 'discord_username'];


    /**
     * @return HasOne
     */
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function bumper(): HasOne
    {
        return $this->hasOne(Bumper::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function cringeCounter(): HasOne
    {
        return $this->hasOne(CringeCounter::class, 'user_id', 'id');
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function isAdmin(string $id): bool
    {
        return !DiscordUser::where('discord_id', $id)->has('admin')->get()->isEmpty();
    }

    /**
     * @param string $id
     * @param int $level
     * @return bool
     */
    public static function hasLevel(string $id, int $level): bool
    {
        return !DiscordUser::where('discord_id', $id)->whereRelation('admin', 'level', '>=', $level)->get()->isEmpty();
    }

    /**
     * @param string $id
     * @param int $level
     * @return bool
     */
    public static function hasHigherLevel(string $id, int $level): bool
    {
        return !DiscordUser::where('discord_id', $id)->whereRelation('admin', 'level', '>', $level)->get()->isEmpty();
    }
}
