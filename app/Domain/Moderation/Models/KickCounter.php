<?php

namespace App\Domain\Moderation\Models;

use App\Domain\Discord\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KickCounter extends Model
{

    protected $table = 'kick_counter';

    protected $fillable = ['count', 'guild_id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
