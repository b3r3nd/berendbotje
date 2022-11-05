<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiscordUser extends Model
{
    use HasFactory;

    protected $table = 'discord_users';
    protected $fillable = ['discord_id'];


    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'discord_user_roles', 'user_id');
    }

    public function rolesByGuild(string $guildId)
    {
        $guild = Guild::get($guildId);
        return $this->roles->where('guild_id', '=', $guild->id);
    }

    public function permissionsByGuild(Guild $guild): array
    {
        $permissions = [];
        foreach ($this->roles->where('guild_id', $guild->id) as $role) {
            foreach ($role->permissions as $perm) {
                $permissions[] = $perm->name;
            }
        }
        return $permissions;
    }

    public static function hasPermission(string $userId, string $guildId, string $permissionName): bool
    {
        $guild = Guild::get($guildId);
        $user = DiscordUser::get($userId);
        $permissionName = strtolower($permissionName);
        return in_array($permissionName, $user->permissionsByGuild($guild) ?? []);
    }


    public static function get($discordId)
    {
        return DiscordUser::firstOrCreate(['discord_id' => $discordId]);
    }

    /**
     * @return string
     */
    public function tag(): string
    {
        return "<@{$this->discord_id}>";
    }

    /**
     * @param string $id
     * @param string $guildId
     * @return bool
     */
    public static function isAdmin(string $id, string $guildId): bool
    {
        return !DiscordUser::where(['discord_id' => $id, 'guild_id' => $guildId])->has('admin')->get()->isEmpty();
    }

    /**
     * @param string $id
     * @param string $guildId
     * @param int $level
     * @return bool
     */
    public static function hasLevel(string $id, string $guildId, int $level): bool
    {
        if ($level == 0) {
            return true;
        }
        return !DiscordUser::where(['discord_id' => $id, 'guild_id' => $guildId])->whereRelation('admin', 'level', '>=', $level)->get()->isEmpty();
    }

    /**
     * @param string $id
     * @param string $guildId
     * @param int $level
     * @return bool
     */
    public static function hasHigherLevel(string $id, string $guildId, int $level): bool
    {
        if ($level == 0) {
            return true;
        }
        return !DiscordUser::where('discord_id', $id)->where('guild_id', $guildId)->whereRelation('admin', 'level', '>', $level)->get()->isEmpty();
    }

    /**
     * @return HasOne
     */
    public function messageCounter(): HasOne
    {
        return $this->hasOne(MessageCounter::class, 'user_id', 'id');
    }

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
     * @return HasOne
     */
    public function banCounter(): HasOne
    {
        return $this->hasOne(BanCounter::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function kickCounter(): HasOne
    {
        return $this->hasOne(KickCounter::class, 'user_id', 'id');
    }
}
