<?php

namespace Modules\Product\Interfaces\V1;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Product\Models\Price;
use Modules\Shared\Enums\CurrencyEnum;

interface PricableInterface
{
    /**
     * Get the prices relationship.
     */
    public function prices(): BelongsToMany;

    /**
     * Get the current valid prices relationship.
     */
    public function currentValidPrices(): Collection;

    /**
     * Get the regular price relationship.
     */
    public function regularPrice(): Collection;

    /**
     * Get the offer price relationship.
     */
    public function offerPrice(): Collection;

    /**
     * Get the current prices for a specific currency.
     */
    public function currentPricesForCurrency(CurrencyEnum $currency): Collection;

    /**
     * Get the offer prices for a specific currency.
     */
    public function offerPricesForCurrency(CurrencyEnum $currency): Collection;

    /**
     * Get the regular current price for a specific currency.
     */
    public function regularCurrentPriceForCurrency(CurrencyEnum $currency): Price;

    /**
     * Get the offer current price for a specific currency.
     */
    public function offerCurrentPriceForCurrency(CurrencyEnum $currency): Price;
}
