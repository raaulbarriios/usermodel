<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'token'])]
class Token extends Model
{
    /**
     * Obtener el usuario asociado al token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
