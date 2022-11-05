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


    public static function get(string $guildId)
    {
        return Guild::where('guild_id', $guildId)->first();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(DiscordUser::class, 'owner_id', 'id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function mediaChannels(): HasMany
    {
        return $this->hasMany(MediaChannel::class);
    }
}
