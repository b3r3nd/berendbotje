<?php /** @noinspection PhpParamsInspection */

namespace App\Discord\Roles\Models;

use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use Database\Factories\GuildFactory;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'guild_id', 'is_admin'];


    /**
     * @return RoleFactory
     */
    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
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
     * @param string $guildId
     * @param string $roleName
     * @return mixed
     */
    public static function get(string $guildId, string $roleName): mixed
    {
        $guild = Guild::get($guildId);

        return self::where([
            ['guild_id', '=', $guild->id],
            ['name', '=', strtolower($roleName)]
        ])->first();
    }

    /**
     * @param string $guildId
     * @param string $roleName
     * @return bool
     */
    public static function exists(string $guildId, string $roleName): bool
    {
        $guild = Guild::get($guildId);
        return !self::where([
            ['guild_id', '=', $guild->id],
            ['name', '=', strtolower($roleName)]
        ])->get()->isEmpty();
    }

    /**
     * @param string $guildId
     * @return mixed
     */
    public static function byGuildId(string $guildId): mixed
    {
        return self::where('guild_id', $guildId);
    }

    /**
     * @param string $guildId
     * @return mixed
     */
    public static function byDiscordGuildId(string $guildId): mixed
    {
        $guild = Guild::get($guildId);
        return self::where('guild_id', $guild->id);
    }

    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(DiscordUser::class, 'discord_user_roles', 'role_id', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }
}
