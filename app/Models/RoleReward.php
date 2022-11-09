<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleReward extends Model
{
    protected $table = 'rewards';
    protected $fillable = ['level', 'role', 'guild_id'];


    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }

    public static function byGuild($guildId)
    {
        return RoleReward::where(['guild_id' => Guild::get($guildId)->id]);
    }

    public function roleTag(): string
    {
        return "<@&{$this->role}>";
    }
}
