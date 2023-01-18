<?php /** @noinspection ALL */

namespace App\Models;

use App\Discord\Core\Scopes\PermissionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_admin'];

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
