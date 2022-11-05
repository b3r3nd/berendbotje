<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    public static function exists(string $name): bool
    {
        return !Permission::where('name', strtolower($name))->get()->isEmpty();
    }

    public static function get(string $name)
    {
        return Permission::where('name', strtolower($name))->first();
    }
}
