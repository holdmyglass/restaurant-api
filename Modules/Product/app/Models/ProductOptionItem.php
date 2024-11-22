<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\Models\Observers\BlamableTrait;
use Modules\Shared\Models\Observers\VersionableTrait;
use Spatie\Translatable\HasTranslations;

class ProductOptionItem extends Model
{
    use BlamableTrait, HasTranslations, HasUuids, SoftDeletes, VersionableTrait {
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

    protected $fillable = [
        'name',
        'slug',
        'description',
        'vat',
        'min',
        'max',
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
        ];
    }

    public function prices(): MorphMany
    {
        return $this->morphMany(Price::class, 'priceable');
    }

    public function getDistinctPrices()
    {
        // Fetch all prices associated with the product where is_current_version is true
        $prices = $this->prices()->where('is_current_version', true)->latest()->get();

        // Initialize a collection to hold the distinct prices
        $distinctPrices = collect();

        // Loop through the prices to group by price_type and currency
        foreach ($prices as $price) {
            // Check if the distinctPrices collection already has a price with the same price_type and currency
            if (! $distinctPrices->contains(function ($existingPrice) use ($price) {
                return $existingPrice->price_type === $price->price_type && $existingPrice->currency === $price->currency;
            })) {
                // If not, add the price to the distinctPrices collection
                $distinctPrices->push($price);
            }
        }

        // Return the collection of distinct Price model instances
        return $distinctPrices;
    }

    public function items(): BelongsToMany
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
