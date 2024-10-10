<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\Models\Observers\BlamableTrait;
use Modules\Shared\Models\Observers\VersionableTrait;
use Spatie\Translatable\HasTranslations;

class ProductOption extends Model
{
    use BlamableTrait, HasTranslations,  HasUuids , SoftDeletes, VersionableTrait;

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
    ];

    public $translatable = ['name', 'description'];

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
