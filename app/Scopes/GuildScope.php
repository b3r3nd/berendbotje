<?php

namespace App\Scopes;

use App\Discord\Core\Bot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GuildScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        Bot::getDiscord();



        $builder->where('guild_id', '=', '590941503917129743');
    }
}
