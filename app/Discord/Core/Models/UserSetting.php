<?php

namespace App\Discord\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'user_id', 'guild_id'];


    /**
     * @param string $guildId
     * @return mixed
     */
    public static function byUserId(string $userId): mixed
    {
        return self::where(['user_id' => DiscordUser::get($userId)->id]);
    }


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class);
    }
}
