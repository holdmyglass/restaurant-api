<?php

namespace Modules\Product\Interfaces\V1;

use Modules\Product\Http\Requests\V1\CreateProductOptionRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionRequest;
use Modules\Product\Models\ProductOption;

interface WriteProductOptionRepositoryInterface
{
    public function store(CreateProductOptionRequest $request): ProductOption;

    public function update(UpdateProductOptionRequest $request, ProductOption $productOption): ProductOption;

    public function destroy(ProductOption $productOption): bool;
}
