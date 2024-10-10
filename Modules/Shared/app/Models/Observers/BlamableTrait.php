<?php

namespace Modules\Shared\Models\Observers;

use Illuminate\Support\Facades\Auth;
use Modules\Shared\Services\V1\SystemService;

trait BlamableTrait
{
    protected static function bootBlamableTrait(): void
    {
        // TODO: Need to use the Authenticated profile id insted of User id
        static::creating(function ($model) {
            $model->created_by = Auth::check() ? Auth::id() : (new SystemService)->getSystemId();
            $model->updated_by = Auth::check() ? Auth::id() : (new SystemService)->getSystemId();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::check() ? Auth::id() : (new SystemService)->getSystemId();
        });

        static::deleting(function ($model) {
            $model->deleted_by = Auth::check() ? Auth::id() : (new SystemService)->getSystemId();
            $model->save();
        });
    }
}
