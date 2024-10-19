<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Enums\ProductLevelEnum;
use Modules\Product\Interfaces\V1\PricableInterface;
use Modules\Product\Models\Traits\PricableTrait;
use Modules\Shared\Models\Observers\BlamableTrait;
use Modules\Shared\Models\Observers\VersionableTrait;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements PricableInterface
{
    use BlamableTrait, HasTranslations, HasUuids, PricableTrait, SoftDeletes, VersionableTrait {
        VersionableTrait::getCasts as getVersionableCasts;
        HasTranslations::getCasts as getTranslatableCasts;
    }

    public function getCasts()
    {

        $casts = array_filter(
            array_merge($this->getVersionableCasts(), $this->getTranslatableCasts()),
            fn ($key) => array_key_exists($key, $this->getVersionableCasts() + $this->getTranslatableCasts())
        );

        return $casts;
    }

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
        'available',
        'takeaway',
        'delivery',
        'eat_in',
        'offer',
        'rank',
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
