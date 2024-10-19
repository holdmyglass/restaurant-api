<?php

namespace Modules\Product\Interfaces\V1;

use Modules\Product\Http\Requests\V1\CreateProductRequest;
use Modules\Product\Http\Requests\V1\UpdateProductRequest;
use Modules\Product\Models\Product;

interface WriteProductRepositoryInterface
{
    public function store(CreateProductRequest $request): Product;

    public function update(UpdateProductRequest $request, Product $product): Product;

    public function destroy(Product $product): bool;
}
