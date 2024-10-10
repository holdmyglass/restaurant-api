<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\Models\Observers\BlamableTrait;
use Modules\Shared\Models\Observers\VersionableTrait;
use Spatie\Translatable\HasTranslations;

class ProductOptionItem extends Model
{
    use BlamableTrait, HasTranslations, HasUuids, SoftDeletes, VersionableTrait;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'vat',
    ];

    public $translatable = ['name', 'description'];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function price(): MorphMany
    {
        return $this->morphMany(Price::class, 'pricable')
            ->where('is_current_version', true)
            ->where(function ($query) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->latest();
    }

    /**
     * The list of the fields that need to be preserved
     * If these fields are changed a new version is created
     *
     * @return array<int, string>
     */
    protected function getPreservableAttributes()
    {
        return [
            'name',
        ];
    }
}
