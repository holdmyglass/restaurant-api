<?php

namespace Modules\Product\Models\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Product\Enums\PriceTypeEnum;
use Modules\Product\Models\Price;
use Modules\Shared\Enums\CurrencyEnum;

trait PricableTrait
{
    public function prices(): BelongsToMany
    {
        return $this->belongsToMany(Price::class, 'price_pricable', 'pricable_id', 'price_id');
    }

    public function currentValidPrices(): Collection
    {
        return $this->prices()->where('is_current_version', true)
            ->when($this->valid_from, function ($query) {
                $query->where('valid_from', '<=', now());
            })
            ->when($this->valid_until, function ($query) {
                $query->where('valid_until', '>=', now());
            })
            ->latest()
            ->get();
    }

    public function regularPrice(): Collection
    {
        return $this->currentValidPrices()->where('price_type', PriceTypeEnum::REGULAR);
    }

    public function offerPrice(): Collection
    {
        return $this->currentValidPrices()->where('price_type', PriceTypeEnum::OFFER);
    }

    public function currentPricesForCurrency(CurrencyEnum $currency): Collection
    {
        return $this->currentValidPrices()->where('currency', $currency)->latest()->get();
    }

    public function offerPricesForCurrency(CurrencyEnum $currency): Collection
    {
        return $this->offerPrice()->where('currency', $currency)->latest()->get();
    }

    public function regularCurrentPriceForCurrency(CurrencyEnum $currency): Price
    {
        return $this->regularPrice()->where('currency', $currency)->first();
    }

    public function offerCurrentPriceForCurrency(CurrencyEnum $currency): Price
    {
        return $this->offerPrice()->where('currency', $currency)->first();
    }

    public static function bootPricableTrait()
    {
        static::deleting(function ($model) {
            $model->prices()->detach();
        });
    }
}
