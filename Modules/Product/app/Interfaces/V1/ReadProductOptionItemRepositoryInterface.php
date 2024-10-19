<?php

namespace Modules\Product\Interfaces\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductOptionItem;

interface ReadProductOptionItemRepositoryInterface
{
    public function getAllItems(): Collection;

    public function getItemById(string $id): ProductOptionItem;

    public function getItemByOptionId(string $optionId): Collection;
}
