<?php

namespace App\Models;


use App\Discord\Core\Scopes\PermissionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscordUser extends Model
{
    use HasFactory;

    protected $table = 'discord_users';
    protected $fillable = ['discord_id'];

    /**
     * @param string $userId
     * @param string $guildId
     * @param string $permissionName
     * @return bool
     */
    public static function hasPermission(string $userId, string $guildId, string $permissionName): bool
    {
        $guild = Guild::get($guildId);
        $user = DiscordUser::get($userId);
        $permissionName = strtolower($permissionName);

        return in_array($permissionName, $user->permissionsByGuild($guild) ?? [], true);
    }

    /**
     * @param $discordId
     * @return mixed
     */
    public static function get($discordId): mixed
    {
        return self::firstOrCreate(['discord_id' => $discordId]);
    }


    /**
     * @param string $guildId
     * @return mixed
     */
    public function rolesByGuild(string $guildId): mixed
    {
        $guild = Guild::get($guildId);
        return $this->roles->where('guild_id', '=', $guild->id);
    }

    /**
     * @param Guild $guild
     * @return array
     */
    public function permissionsByGuild(Guild $guild): array
    {
        $permissions = [];
        foreach ($this->roles->where('guild_id', $guild->id) as $role) {
            foreach ($role->permissions()->withoutGlobalScope(PermissionScope::class)->get() as $perm) {
                $permissions[] = $perm->name;
            }
        }
        return $permissions;
    }

    /**
     * @return string
     */
    public function tag(): string
    {
        return "<@{$this->discord_id}>";
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'discord_user_roles', 'user_id');
    }

    /**
     * @return hasMany
     */
    public function givenTimeouts(): hasMany
    {
        return $this->hasMany(Timeout::class, 'giver_id', 'id');
    }

    /**
     * @return hasMany
     */
    public function messageCounters(): hasMany
    {
        return $this->hasMany(MessageCounter::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function bumpCounters(): HasMany
    {
        return $this->hasMany(Bumper::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function cringeCounters(): HasMany
    {
        return $this->hasMany(CringeCounter::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function banCounters(): HasMany
    {
        return $this->hasMany(BanCounter::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function kickCounters(): HasMany
    {
        return $this->hasMany(KickCounter::class, 'user_id', 'id');
    }
}
