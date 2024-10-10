<?php

namespace Modules\Product\Services\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;

class ProductService
{
    public function getAllProducts(): Collection
    {
        return Product::all();
    }
}
