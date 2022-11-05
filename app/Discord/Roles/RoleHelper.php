<?php

namespace App\Discord\Roles;

use App\Discord\Core\EmbedFactory;
use App\Models\Permission;

class RoleHelper
{

    public static function processPermissions($parameters)
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
