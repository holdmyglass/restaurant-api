<?php

namespace Modules\Shared\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\Enums\TokenableTypeEnum;
use Modules\Shared\Enums\TokenScopeEnum;
use Modules\Shared\Enums\TokenTypeEnum;
use Modules\Shared\Models\Observers\BlamableTrait;

class Token extends Model
{
    use BlamableTrait, HasUuids, SoftDeletes;

    protected $fillable = [
        'token',
        'scope',
        'type',
        'tokenable_id',
        'tokenable_type',
        'user_at',
        'expires_at',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'scope' => TokenScopeEnum::class,
        'tokenable_type' => TokenableTypeEnum::class,
        'type' => TokenTypeEnum::class,

    ];

    // Get the parent tokenable model.
    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }
}
