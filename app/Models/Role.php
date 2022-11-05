<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'guild_id'];


    public static function get(string $guildId, string $roleName)
    {
        $guild = Guild::get($guildId);

        return Role::where([
            ['guild_id', '=', $guild->id],
            ['name', '=', strtolower($roleName)]
        ])->first();
    }

    public static function exists(string $guildId, string $roleName): bool
    {
        $guild = Guild::get($guildId);
        return !Role::where([
            ['guild_id', '=', $guild->id],
            ['name', '=', strtolower($roleName)]
        ])->get()->isEmpty();
    }

    public static function byGuildId(string $guildId)
    {
        return Role::where('guild_id', $guildId);
    }

    public static function byDiscordGuildId(string $guildId)
    {
        $guild = Guild::get($guildId);
        return Role::where('guild_id', $guild->id);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }
}
