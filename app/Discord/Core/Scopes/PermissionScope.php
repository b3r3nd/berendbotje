<?php

namespace App\Discord\Core\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PermissionScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('is_admin', '=', false);
    }
}
