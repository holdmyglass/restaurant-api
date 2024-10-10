<?php

namespace Modules\Product\Interfaces\V1;

use Modules\Product\DTO\ProductCategoryDTO;
use Modules\Product\Models\ProductCategory;

interface WriteProductCategoryRepositoryInterface
{
    public function store(ProductCategoryDTO $productcategoryDTO): ProductCategory;

    public function update(ProductCategoryDTO $productcategoryDTO, ProductCategory $productCategory): ProductCategory;

    public function destroy(ProductCategory $productcategory): bool;
}
