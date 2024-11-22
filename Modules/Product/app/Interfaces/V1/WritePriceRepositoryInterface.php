<?php

namespace Modules\Product\Interfaces\V1;

use Modules\Product\Models\Price;

interface WritePriceRepositoryInterface
{
    public function store(object $priceData, string $priceableType, string $priceableId): Price;

    public function update(object $priceData, Price $price): Price;
}
