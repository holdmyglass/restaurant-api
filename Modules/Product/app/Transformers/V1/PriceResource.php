<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shared\Enums\CurrencyEnum;

class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {

        return [
            'price' => $this->formatPrice($this->price, $this->currency),
            'currency' => $this->currency,
        ];
    }

    /**
     * Format the price value based on the currency.
     */
    private function formatPrice(int $price, CurrencyEnum $currency): float
    {
        return match ($currency) {
            CurrencyEnum::USD, CurrencyEnum::EUR => $price / 100,
            default => $price,
        };
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPrice(): float
    {
        return $this->formatPrice($this->price, $this->currency);
    }
}
