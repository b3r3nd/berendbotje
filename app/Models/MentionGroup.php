<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentionGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'guild_id', 'is_custom'];


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
        return MentionGroup::where('guild_id', Guild::get($guildId)->id);
    }
}
