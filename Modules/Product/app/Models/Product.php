<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Enums\ProductLevelEnum;
use Modules\Shared\Models\Observers\BlamableTrait;
use Modules\Shared\Models\Observers\VersionableTrait;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use BlamableTrait, HasTranslations, HasUuids, SoftDeletes, VersionableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'vat',
    ];

    public $translatable = ['name', 'description'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available' => 'boolean',
            'takeaway' => 'boolean',
            'delivery' => 'boolean',
            'eat_in' => 'boolean',
            'promo' => 'boolean',
            'level' => ProductLevelEnum::class,

        ];
    }

    public function categoryHistory(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class)->where('is_current_version', true);
    }

    public function priceHistory(): HasMany
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

    public function optionHistory(): BelongsToMany
    {
        return $this->belongsToMany(ProductOption::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(ProductOption::class)->where('is_current_version', true);
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
