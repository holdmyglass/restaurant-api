<?php

namespace Modules\Product\DTO;

use DateTime;
use InvalidArgumentException;
use Modules\Product\Enums\PriceTypeEnum;
use Modules\Shared\Enums\CurrencyEnum;

class PriceDTO
{
    public function __construct(
        public readonly float $price,
        public readonly CurrencyEnum $currency,
        public readonly string $name,
        public readonly PriceTypeEnum $price_type,
        public readonly ?DateTime $valid_from = null,
        public readonly ?DateTime $valid_until = null,
    ) {}

    public function toArray(): array
    {
        $multiplier = $this->getMultiplier($this->currency);
        $price = $this->price * $multiplier;

        return [
            'price' => $price,
            'currency' => $this->currency,
            'name' => $this->name,
            'price_type' => $this->price_type,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
        ];
    }

    private function getMultiplier(CurrencyEnum $currency): int
    {
        return match ($currency) {
            CurrencyEnum::EUR,CurrencyEnum::USD => 100,
            // Add more currency cases as needed
            default => throw new InvalidArgumentException('Unsupported currency'),
        };
    }
}
