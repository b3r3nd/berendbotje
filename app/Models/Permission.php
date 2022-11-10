<?php

namespace App\Models;

use App\Discord\Core\Scopes\PermissionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_admin'];


    protected static function booted()
    {
        static::addGlobalScope(new PermissionScope());
    }

    public static function exists(string $name): bool
    {
        return !Permission::where('name', strtolower($name))->get()->isEmpty();
    }

    public static function get(string $name)
    {
        return Permission::where('name', strtolower($name))->first();
    }
}
