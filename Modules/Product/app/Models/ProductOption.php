<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Enums\ProductOptionTypeEnum;
use Modules\Shared\Models\Observers\BlamableTrait;
use Modules\Shared\Models\Observers\VersionableTrait;
use Spatie\Translatable\HasTranslations;

class ProductOption extends Model
{
    use BlamableTrait, HasTranslations,  HasUuids , SoftDeletes, VersionableTrait {
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
        'min',
        'max',
        'active',
        'type',
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
            'active' => 'boolean',
            'type' => ProductOptionTypeEnum::class,
        ];
    }

    public function productHistory(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->where('is_current_version', true);
    }

    public function itemHistory(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionItem::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionItem::class)->where('is_current_version', true);
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
            'min',
            'max',
        ];
    }
}
