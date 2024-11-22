<?php

namespace Modules\Product\Repositories\V1;

use Modules\Product\DTO\PriceDTO;
use Modules\Product\Enums\PriceTypeEnum;
use Modules\Product\Interfaces\V1\ReadPriceRepositoryInterface;
use Modules\Product\Interfaces\V1\WritePriceRepositoryInterface;
use Modules\Product\Models\Price;
use Modules\Shared\Enums\CurrencyEnum;

class PriceRepository implements ReadPriceRepositoryInterface, WritePriceRepositoryInterface
{
    public function store(object $priceData, string $priceableType, string $priceableId): Price
    {
        // Create the PriceDTO from the incoming data
        $priceDto = $this->createPriceDto($priceData);

        // Check if a Price already exists for the same pricable_type, currency, and price_type
        $existingPrice = Price::where('priceable_type', $priceableType)
            ->where('priceable_id', $priceableId)
            ->where('currency', $priceDto->currency->value) // Assuming currency is an enum
            ->where('price_type', $priceDto->price_type->value) // Assuming price_type is an enum
            ->first();

        // If an existing Price is found, delete it
        if ($existingPrice) {
            $existingPrice->delete();
        }

        $priceDto = $this->createPriceDto($priceData);

        $price = new Price;
        $price->fill($priceDto->toArray());

        // Set the polymorphic relationship fields
        $price->priceable_type = $priceableType;
        $price->priceable_id = $priceableId;

        // Save the new Price
        $price->save();

        return $price;
    }

    public function update(object $priceData, Price $price): Price
    {
        $priceDto = $this->createPriceDto($priceData);

        $price->fill($priceDto->toArray());

        // save the new version in a variable before returning otherwise it will return the old version
        $product = $price->saveWithVersion();

        return $product;
    }

    private function createPriceDto(object $priceData): PriceDTO
    {
        return new PriceDTO(
            price: $priceData->price ?? 0,
            currency: CurrencyEnum::from($priceData->currency) ?? CurrencyEnum::EUR,
            name: $request->price_name ?? ucfirst(strtolower(PriceTypeEnum::REGULAR->value)).' Price',
            price_type: is_null($priceData->price_type) || ! PriceTypeEnum::isValid($priceData->price_type)
                ? PriceTypeEnum::REGULAR
                : PriceTypeEnum::from($priceData->price_type),
            valid_from: $request->valid_from ?? null,
            valid_until: $request->valid_until ?? null
        );
    }
}
