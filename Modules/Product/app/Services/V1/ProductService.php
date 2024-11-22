<?php

namespace Modules\Product\Services\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\File\Enums\ImageSizeEnum;
use Modules\File\Services\V1\ImageUploadService;
use Modules\Product\Http\Requests\V1\CreateProductRequest;
use Modules\Product\Http\Requests\V1\UpdateProductRequest;
use Modules\Product\Interfaces\V1\ReadPriceRepositoryInterface;
use Modules\Product\Interfaces\V1\ReadProductRepositoryInterface;
use Modules\Product\Interfaces\V1\WritePriceRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductRepositoryInterface;
use Modules\Product\Models\Product;
use Modules\Product\Transformers\V1\ProductCategoryResource;
use Modules\Product\Transformers\V1\ProductResource;
use Modules\Shared\Enums\ServerStatusCodeEnum;

class ProductService
{
    public function __construct(
        private readonly ReadProductRepositoryInterface $readProduct,
        private readonly WriteProductRepositoryInterface $writeProduct,
        private readonly ReadPriceRepositoryInterface $readPrice,
        private readonly WritePriceRepositoryInterface $writePrice,
        private readonly ProductCategoryService $productCategoryService,
        private readonly ImageUploadService $imageUploadService
    ) {}

    /**
     * Return all the current product
     *
     * @return array<string, mixed>
     */
    public function getProducts(): array
    {
        $products = $this->readProduct->getAllProducts();

        $current_products = $products->filter(function ($products) {
            return $products->is_current_version;
        });

        return
        [
            'data' => [
                'products' => ProductResource::collection($current_products),
                'count' => count($current_products),
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
    public function getAllProducts(): array
    {
        $products = $this->readProduct->getAllProducts();

        return
        [
            'data' => [
                'products' => ProductResource::collection($products),
                'count' => count($products),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Return a product by id
     *
     * @return array<string, mixed>
     */
    public function getProductById(string $id): array
    {
        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        return
        [
            'data' => [
                'product' => new ProductResource($this->getProductFromId($id)),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Get the Current Product for given Product ID
     */
    public function getCurrentProductById(string $id): Product
    {

        $product = $this->readProduct->getProductById($id);
        if ($product->is_current_version) {
            return $product;
        }

        $currentProduct = $this->readProduct->getProductsByVersionIdentifier($product->version_identifier)
            ->filter(function ($product) {
                return $product->is_current_version;
            })
            ->latest()
            ->first();

        return $currentProduct;
    }

    /**
     * Get the Current Product for given Product version identifier
     */
    public function getCurrentProductByVersionIdentifier(string $versionIdentifier): Product
    {
        $currentProduct = $this->readProduct->getProductsByVersionIdentifier($versionIdentifier)
            ->filter(function ($product) {
                return $product->is_current_version;
            })
            ->latest()
            ->first();

        return $currentProduct;
    }

    /**
     * Create new Product
     *
     * @return array<string, mixed>
     */
    public function createProduct(CreateProductRequest $request): array
    {
        $productRequest = $request->except(['price', 'category', 'option']);

        $image = null;

        if ($request->image) {
            $image = $this->imageUploadService->upload($request->file('image'), [ImageSizeEnum::AVATAR, ImageSizeEnum::SMALLSQUARE, ImageSizeEnum::SMALLHORIZONTAL, ImageSizeEnum::SMALLVERTICAL,  ImageSizeEnum::MEDIUMSQUARE, ImageSizeEnum::MEDIUMHORIZONTAL, ImageSizeEnum::MEDIUMHORIZONTAL]);
        }

        if ($image) {
            $productRequest['image'] = $image; // Add the image to the request array
        }

        $product = $this->writeProduct->store(new CreateProductRequest($productRequest));

        if (! is_null($request->price)) {
            foreach ($request->price as $price) {
                if ($price !== null) {
                    $saved_price = $this->writePrice->store((object) $price, Product::class, $product->id);
                }
            }
        }

        try {
            if ($request->category !== null) {

                $product->categories()->detach();

                foreach ($request->category as $categoryId) {
                    if (! is_null($categoryId)) {
                        $product->categories()->attach($categoryId);
                    }
                }

            }

        } catch (QueryException $e) {

            // TODO: LOG This errorError
            // $errorMessage = $e->getMessage();

            throw new QueryException(
                $e->getConnectionName(),
                $e->getSql(),
                $e->getBindings(),
                $e // Pass the original exception for chaining
            );
        }

        try {
            if ($request->option !== null) {

                $product->options()->detach();

                foreach ($request->option as $optionId) {
                    if (! is_null($optionId)) { // Check if each category ID is not null
                        $product->options()->attach($optionId); // Attach the category to the product
                    }
                }

            }

        } catch (QueryException $e) {

            // TODO: LOG This errorError
            // $errorMessage = $e->getMessage();

            throw new QueryException(
                $e->getConnectionName(),
                $e->getSql(),
                $e->getBindings(),
                $e // Pass the original exception for chaining
            );
        }

        return
        [
            'data' => [
                'product' => new ProductResource($product),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Create new Product
     *
     * @return array<string, mixed>
     */
    public function updateProduct(UpdateProductRequest $request, string $id): array
    {

        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        $product = $this->readProduct->getProductById($id);
        $productRequest = $request->except(['price', 'category', 'option']);
        $product = $this->writeProduct->update(new UpdateProductRequest($productRequest), $product);

        try {
            if (! is_null($request->category)) { // Check if $request->category is not null

                $product->categories()->detach();

                foreach ($request->category as $categoryId) { // Iterate over each category ID
                    if (! is_null($categoryId)) { // Check if each category ID is not null
                        $product->categories()->attach($categoryId); // Attach the category to the product
                    }
                }

            }
        } catch (QueryException $e) {

            // TODO: LOG This errorError
            // $errorMessage = $e->getMessage();

            throw new QueryException(
                $e->getConnectionName(),
                $e->getSql(),
                $e->getBindings(),
                $e // Pass the original exception for chaining
            );
        }

        return
        [
            'data' => [
                'product' => new ProductResource($product),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Delete Product
     *
     * @return array<string, mixed>
     */
    public function deleteProduct(string $id): array
    {
        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        $this->writeProduct->destroy($this->getProductFromId($id));

        return
        [
            'status_code' => ServerStatusCodeEnum::NO_CONTENT,
            'status' => 'success',
            'message' => __('shared::messages.success.deleted_successfully'),
        ];
    }

    public function getProductList(): array
    {

        $categories = $this->productCategoryService->getCurrentProductCategories();

        return
        [
            'data' => [
                'categories' => ProductCategoryResource::collection($categories),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Return a product by id
     */
    private function getProductFromId(string $id): Product|InvalidArgumentException
    {

        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        try {
            $product = $this->readProduct->getProductById($id);

            if ($product->is_deleted) {
                throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
            }

            return $product;
        } catch (ModelNotFoundException $e) {
            throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
        }

    }
}
