<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Interfaces\V1\PricableInterface;
use Modules\Shared\Models\Observers\BlamableTrait;
use Modules\Shared\Models\Observers\VersionableTrait;

class Price extends Model
{
    use BlamableTrait, HasUuids, SoftDeletes, VersionableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'price',
        'currency',
    ];

    /**
     * The list of the fields that need to be preserved
     * If these fields are changed a new version is created
     *
     * @return array<int, string>
     */
    protected function getPreservableAttributes()
    {
        return [
            'price',
            'currency',
            'name',
            'price_type',
            'valid_from',
            'valid_until',
        ];
    }

    public function pricables(): MorphToMany
    {

        return $this->morphToMany(PricableInterface::class, 'pricable', 'price_pricable')
            ->withTimestamps();
    }
}
