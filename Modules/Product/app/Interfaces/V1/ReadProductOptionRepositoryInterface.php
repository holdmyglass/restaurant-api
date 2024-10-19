<?php

namespace Modules\Product\Interfaces\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductOption;

interface ReadProductOptionRepositoryInterface
{
    public function getAllOptions(): Collection;

    public function getOptionById(string $id): ProductOption;

    public function getProductOptionsByProductId(string $productId): Collection;
}
