<?php

namespace Modules\Product\Interfaces\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductCategory;

interface ReadProductCategoryRepositoryInterface
{
    public function getAllProductCategories(): Collection;

    public function getProductCategoryById(string $id): ProductCategory;

    public function getProductCategoriesByVersionIdentifier(string $versionIdentifier): Collection;
}
