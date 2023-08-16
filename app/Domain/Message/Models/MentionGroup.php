<?php

namespace App\Domain\Message\Models;

use App\Domain\Discord\Guild;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentionGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'guild_id', 'is_custom', 'has_role', 'has_user', 'multiplier'];


    /**
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(MentionReply::class, 'group_id');
    }

    /**
     * @param $guildId
     * @return mixed
     */
    public static function byGuild($guildId): mixed
    {
        return self::where('guild_id', Guild::get($guildId)->id);
    }
}
