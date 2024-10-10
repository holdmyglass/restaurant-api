<?php

namespace Modules\Product\Interfaces\V1;

use Modules\Product\Http\Requests\V1\CreateProductRequest;
use Modules\Product\Http\Requests\V1\UpdateProductRequest;
use Modules\Product\Models\Product;

interface WriteProductRepositoryInterface
{
    public function createProduct(CreateProductRequest $request);

    public function updateProduct(UpdateProductRequest $request, Product $product);

    public function deleteProduct(Product $product);
}
