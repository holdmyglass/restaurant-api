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
    public function store(object $priceData): Price
    {
        $priceDto = $this->createPriceDto($priceData);

        $price = new Price;
        $price->fill($priceDto->toArray());
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
