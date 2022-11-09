<?php

namespace App\Discord\Roles;

use App\Discord\Core\EmbedFactory;
use App\Models\Permission;
use Illuminate\Support\Collection;

class RoleHelper
{
    /**
     * Returns an array with given permission names or false when invalid permissions is
     * provided.
     *
     * @param $parameters
     * @return false|Collection
     */
    public static function processPermissions($parameters): bool|Collection
    {
        $parameters = strtolower($parameters);
        if (str_contains($parameters, ',')) {
            $permissions = explode(',', $parameters);
        } else {
            $permissions[] = $parameters;
        }

        $attach = collect([]);
        foreach ($permissions as $permission) {
            if (!Permission::exists($permission)) {
                return false;
            } else {
                $attach->push(Permission::get($permission));
            }
        }
        return $attach;
    }
}
