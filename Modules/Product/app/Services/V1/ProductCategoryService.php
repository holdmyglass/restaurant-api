<?php

namespace Modules\Product\Services\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Product\DTO\ProductCategoryDTO;
use Modules\Product\Http\Requests\V1\CreateProductCategoryRequest;
use Modules\Product\Http\Requests\V1\UpdateProductCategoryRequest;
use Modules\Product\Interfaces\V1\ReadProductCategoryRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductCategoryRepositoryInterface;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Transformers\V1\ProductCategoryCollection;
use Modules\Product\Transformers\V1\ProductCategoryResource;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Services\V1\HelperService;

class ProductCategoryService
{
    public function __construct(
        private readonly ReadProductCategoryRepositoryInterface $read,
        private readonly WriteProductCategoryRepositoryInterface $write
    ) {}

    /**
     * Return all the current product categories
     *
     * @return array<string, mixed>
     */
    public function getProductCategories(): array
    {
        $categories = $this->read->getAllProductCategories();

        $current_categories = $categories->filter(function ($category) {
            return $category->is_current_version;
        });

        return
        [
            'data' => [
                'categories' => new ProductCategoryCollection($current_categories),
                'count' => count($current_categories),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Return all categories including all older versions
     *
     * @return array<string, mixed>
     */
    public function getAllProductCategories(): array
    {
        $categories = $this->read->getAllProductCategories();

        return
        [
            'data' => [
                'categories' => new ProductCategoryCollection($categories),
                'count' => count($categories),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Return a product category by id
     *
     * @return array<string, mixed>
     */
    public function getproductCategoryById(string $id): array
    {

        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        try {
            $category = $this->read->getProductCategoryById($id);

            if ($category->is_deleted) {
                throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
            }

            return
            [
                'data' => [
                    'category' => new ProductCategoryResource($category),
                ],
                'status_code' => ServerStatusCodeEnum::OK,
                'status' => 'success',
            ];
        } catch (ModelNotFoundException $e) {
            throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
        }

    }

    /**
     * Get the Current Product Category for given  Product Category ID
     */
    public function getCurrentProductCategoryById(string $id): ProductCategory
    {

        $productCategory = $this->read->getProductCategoryById($id);
        if ($productCategory->is_current_version) {
            return $productCategory;
        }

        $currentProductCategory = $this->read->getProductCategoriesByVersionIdentifier($productCategory->version_identifier)
            ->filter(function ($category) {
                return $category->is_current_version;
            })
            ->latest()
            ->first();

        return $currentProductCategory;
    }

    /**
     * Get the Current Product Category for given Product Category version identifier
     */
    public function getCurrentProductCategoryByVersionIdentifier(string $versionIdentifier): ProductCategory
    {
        $currentProductCategory = $this->read->getProductCategoriesByVersionIdentifier($versionIdentifier)
            ->filter(function ($category) {
                return $category->is_current_version;
            })
            ->latest()
            ->first();

        return $currentProductCategory;
    }

    /**
     * Create new Product category
     *
     * @return array<string, mixed>
     */
    public function createProductCategory(CreateProductCategoryRequest $request): array
    {

        $poductCategoryDTO = new ProductCategoryDTO(
            parentId: $request->parentId ?? null,
            name: $request->name,
            description: $request->description ?? [],
            //TODO Need to work on this image, create Image upload method in File Module
            image: $request->image ?? null,
            slug: HelperService::Slugify(
                $request->slug ?? HelperService::getFallbackSlugAttributeFromTranslatable($request->name),
                'slug',
                new ProductCategory
            ),
            rank: HelperService::rankify(new ProductCategory, $request->rank ?? null),
            type: $request->type ?? null,
        );

        $category = $this->write->store($poductCategoryDTO);

        return
        [
            'data' => [
                'category' => new ProductCategoryResource($category),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Update Product Category with new data
     *
     * @return array<string, mixed>
     */
    public function updateProductCategory(UpdateProductCategoryRequest $request, string $id): array
    {
        $productCategory = $this->read->getProductCategoryById($id);

        $poductCategoryDTO = new ProductCategoryDTO(
            parentId: ($request->parent_id ?? (HelperService::isColumnNullable('product_categories', 'parent_id') ? null : $productCategory->parent_id)),
            name: $request->name ?? $productCategory->name,
            description: $request->description ?? $productCategory->description,
            //TODO:  Need to work on this image, create Image upload method in File Module
            image: ($request->image ?? (HelperService::isColumnNullable('product_categories', 'image') ? null : $productCategory->image)),
            slug: $request->slug ?? $productCategory->slug,
            rank: $request->rank ?? $productCategory->rank,
            type: ($request->type ?? (HelperService::isColumnNullable('product_categories', 'type') ? null : $productCategory->type)),
        );

        $category = $this->write->update($poductCategoryDTO, $productCategory);

        return
        [
            'data' => [
                'category' => new ProductCategoryResource($category),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Delete Product Category
     *
     * @return array<string, mixed>
     */
    public function deleteProductCategory(string $id): array
    {
        $productCategory = $this->getproductCategoryById($id);

        $this->write->destroy($productCategory['data']['category']);

        return
        [
            'status_code' => ServerStatusCodeEnum::NO_CONTENT,
            'status' => 'success',
            'message' => __('shared::messages.success.deleted_successfully'),
        ];
    }
}
