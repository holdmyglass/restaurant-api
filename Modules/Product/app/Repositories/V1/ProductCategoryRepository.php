<?php

namespace Modules\Product\Repositories\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\DTO\ProductCategoryDTO;
use Modules\Product\Enums\ProductCategoryTypeEnum;
use Modules\Product\Interfaces\V1\ReadProductCategoryRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductCategoryRepositoryInterface;
use Modules\Product\Models\ProductCategory;

class ProductCategoryRepository implements ReadProductCategoryRepositoryInterface, WriteProductCategoryRepositoryInterface
{
    public function getAllProductCategories(): Collection
    {
        return ProductCategory::all();
    }

    public function getProductCategoryById(string $id): ProductCategory
    {
        return ProductCategory::findOrFail($id);
    }

    public function getProductCategoriesByVersionIdentifier(string $versionIdentifier): Collection
    {
        return ProductCategory::where('version_identifier', $versionIdentifier)->get();
    }

    public function store(ProductCategoryDTO $productCategoryDTO): ProductCategory
    {

        $productCategory = new ProductCategory;
        $productCategory->parent_id = $productCategoryDTO->parentId;

        foreach ($productCategoryDTO->name as $locale => $value) {
            $productCategory->setTranslation('name', $locale, $value);
        }

        $productCategory->slug = $productCategoryDTO->slug;

        foreach ($productCategoryDTO->description as $locale => $value) {
            $productCategory->setTranslation('description', $locale, $value);
        }

        $productCategory->image = $productCategoryDTO->image;
        $productCategory->rank = $productCategoryDTO->rank;
        $productCategory->type = $productcategoryDTO->type ?? ProductCategoryTypeEnum::DISH;
        $productCategory->save();

        return $productCategory;

    }

    public function update(ProductCategoryDTO $productCategoryDTO, ProductCategory $productCategory): ProductCategory
    {

        $productCategory->parent_id = $productCategoryDTO->parentId;

        foreach ($productCategoryDTO->name as $locale => $value) {
            $productCategory->setTranslation('name', $locale, $value);
        }

        $productCategory->slug = $productCategoryDTO->slug;

        foreach ($productCategoryDTO->description as $locale => $value) {
            $productCategory->setTranslation('description', $locale, $value);
        }

        $productCategory->image = $productCategoryDTO->image;
        $productCategory->rank = $productCategoryDTO->rank;
        $productCategory->type = $productcategoryDTO->type ?? ProductCategoryTypeEnum::DISH;

        return $productCategory->saveWithVersion();
    }

    public function destroy(ProductCategory $productcategory): bool
    {
        return $productcategory->deleteWithoutVersion();
    }
}
