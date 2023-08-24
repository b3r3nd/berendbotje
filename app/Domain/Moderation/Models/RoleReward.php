<?php

namespace App\Domain\Moderation\Models;

use App\Domain\Discord\Guild;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleReward extends Model
{
    protected $table = 'rewards';
    protected $fillable = ['level', 'duration', 'role', 'guild_id'];

    /**
     * @return BelongsTo
     */
    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }

    /**
     * @param $guildId
     * @return mixed
     */
    public static function byGuild($guildId): mixed
    {
        return self::where(['guild_id' => Guild::get($guildId)->id]);
    }

    /**
     * @param $guildId
     * @return mixed
     */
    public static function level($guildId): mixed
    {
        return self::where(['guild_id' => Guild::get($guildId)->id])->where(['duration' => null]);
    }

    /**
     * @param $guildId
     * @return mixed
     */
    public static function duration($guildId): mixed
    {
        return self::where(['guild_id' => Guild::get($guildId)->id])->where(['level' => null]);
    }

    /**
     * @return string
     */
    public function roleTag(): string
    {
        return "<@&{$this->role}>";
    }
}
