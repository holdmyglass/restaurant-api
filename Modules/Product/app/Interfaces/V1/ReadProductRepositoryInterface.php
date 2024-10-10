<?php

namespace Modules\Product\Interfaces\V1;

use Modules\Product\Models\Product;

interface ReadProductRepositoryInterface
{
    public function getAllProducts();

    public function getProductById(Product $product);
}
