<?php

namespace Modules\Product\Repositories\V1;

use Modules\Product\Http\Requests\V1\CreateProductRequest;
use Modules\Product\Http\Requests\V1\UpdateProductRequest;
use Modules\Product\Interfaces\V1\ReadProductRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductRepositoryInterface;
use Modules\Product\Models\Product;

class ProductRepository implements ReadProductRepositoryInterface, WriteProductRepositoryInterface
{
    public function getAllProducts() {}

    public function getProductById(Product $product) {}

    public function createProduct(CreateProductRequest $request) {}

    public function updateProduct(UpdateProductRequest $request, Product $product) {}

    public function deleteProduct(Product $product) {}
}
