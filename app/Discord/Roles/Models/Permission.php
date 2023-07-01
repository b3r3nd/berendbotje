<?php /** @noinspection ALL */

namespace App\Discord\Roles\Models;

use App\Discord\Roles\Scopes\PermissionScope;
use Database\Factories\PermissionFactory;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_admin'];


    /**
     * @return PermissionFactory
     */
    protected static function newFactory(): PermissionFactory
    {
        return PermissionFactory::new();
    }

    /**
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new PermissionScope());
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return !self::where('name', strtolower($name))->get()->isEmpty();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function get(string $name): mixed
    {
        return self::where('name', strtolower($name))->first();
    }
}
