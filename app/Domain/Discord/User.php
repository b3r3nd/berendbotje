<?php

namespace App\Domain\Discord;


use App\Domain\Fun\Models\Bump;
use App\Domain\Fun\Models\CringeCounter;
use App\Domain\Fun\Models\UserXP;
use App\Domain\Moderation\Models\BanCounter;
use App\Domain\Moderation\Models\KickCounter;
use App\Domain\Moderation\Models\Timeout;
use App\Domain\Permission\Enums\Permission;
use App\Domain\Permission\Models\Role;
use App\Domain\Permission\Scopes\PermissionScope;
use App\Domain\Setting\Models\UserSetting;
use Database\Factories\DiscordUserFactory;
use Discord\Parts\User\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

    protected $table = 'discord_users';
    protected $fillable = ['discord_id', 'username'];


    /**
     * @return DiscordUserFactory
     */
    protected static function newFactory(): DiscordUserFactory
    {
        return DiscordUserFactory::new();
    }

    /**
     * @param string $userId
     * @param string $guildId
     * @param Permission $permission
     * @return bool
     */
    public static function hasPermission(string $userId, string $guildId, Permission $permission): bool
    {
        $guild = Guild::get($guildId);
        $user = self::get($userId);
        return in_array($permission->value, $user->permissionsByGuild($guild) ?? [], true);
    }

    /**
     * @param string $settingKey
     * @param string $guildId
     * @return bool
     */
    public function enabledSetting(string $settingKey, string $guildId): bool
    {
        $setting = $this->settings()->where([
            ['key', '=', $settingKey],
            ['guild_id', '=', $guildId]
        ])->first();
        return $setting && $setting->value === "1";
    }


    public static function get(Member|string $member): mixed
    {
        if ($member instanceof Member) {
            if ($member->user->discriminator !== "0") {
                $username = "{$member->username}#{$member->user->discriminator}";
            } else {
                $username = $member->username;
            }

            $localUser = self::where('discord_id', $member->id)->first();
            if ($localUser && $username !== $localUser->username) {
                $localUser->update(['username' => $username]);
            }
            return $localUser ?? self::create(['discord_id' => $member->id, 'username' => $username]);
        }

        return self::firstOrCreate(['discord_id' => $member]);
    }

    /**
     * @return string
     */
    public function tag(): string
    {
        return $this->username ? "`{$this->username}`" : "<@{$this->discord_id}>";
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
        return $this->hasMany(UserXP::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function bumpCounters(): HasMany
    {
        return $this->hasMany(Bump::class, 'user_id', 'id');
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


    /**
     * @return HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'user_id', 'id');
    }
}
