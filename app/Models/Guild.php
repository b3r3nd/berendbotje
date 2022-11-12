<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guild extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'guild_id', 'owner_id'];

    /**
     * @param string $guildId
     * @return mixed
     */
    public static function get(string $guildId): mixed
    {
        return Guild::where('guild_id', $guildId)->first();
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class, 'owner_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    /**
     * @return HasMany
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * @return HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * @return HasMany
     */
    public function logSettings(): HasMany
    {
        return $this->hasMany(LogSetting::class);
    }

    /**
     * @return HasMany
     */
    public function mediaChannels(): HasMany
    {
        return $this->hasMany(MediaChannel::class);
    }
}
