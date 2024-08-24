<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileToken extends Model
{
    use HasUuids, SoftDeletes;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
