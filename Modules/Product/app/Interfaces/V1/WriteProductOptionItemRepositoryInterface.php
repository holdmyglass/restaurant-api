<?php

namespace Modules\Product\Interfaces\V1;

use Modules\Product\Http\Requests\V1\CreateProductOptionItemRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionItemRequest;
use Modules\Product\Models\ProductOptionItem;

interface WriteProductOptionItemRepositoryInterface
{
    public function store(CreateProductOptionItemRequest $request): ProductOptionItem;

    public function update(UpdateProductOptionItemRequest $request, ProductOptionItem $productOptionItem): ProductOptionItem;

    public function destroy(ProductOptionItem $productOptionItem): bool;
}
