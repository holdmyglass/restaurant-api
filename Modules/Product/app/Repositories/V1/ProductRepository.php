<?php

namespace Modules\Product\Repositories\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\DTO\ProductDTO;
use Modules\Product\Http\Requests\V1\CreateProductRequest;
use Modules\Product\Http\Requests\V1\UpdateProductRequest;
use Modules\Product\Interfaces\V1\ReadProductRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductRepositoryInterface;
use Modules\Product\Models\Product;
use Modules\Shared\Services\V1\HelperService;

class ProductRepository implements ReadProductRepositoryInterface, WriteProductRepositoryInterface
{
    public function getAllProducts(): Collection
    {
        return Product::all();
    }

    public function getProductById(string $id): Product
    {
        return Product::findOrFail($id);
    }

    public function getProductsByVersionIdentifier(string $versionIdentifier): Collection
    {
        return Product::where('version_identifier', $versionIdentifier)->get();
    }

    public function store(CreateProductRequest $request): Product
    {

        $productDTO = $this->createProductDTO($request);

        $product = new Product;

        // Set translations for name and description
        foreach ($productDTO->name as $locale => $value) {
            $product->setTranslation('name', $locale, $value);
        }

        foreach ($productDTO->description as $locale => $value) {
            $product->setTranslation('description', $locale, $value);
        }

        // Fill the product with the remaining data from the DTO
        $product->fill($productDTO->toArrayExceptTranslatable());

        $product->save();

        return $product;

    }

    public function update(UpdateProductRequest $request, Product $product): Product
    {

        $productDTO = $this->createProductDTO($request, $product);

        // Set translations for name and description
        foreach ($productDTO->name as $locale => $value) {
            $product->setTranslation('name', $locale, $value);
        }

        foreach ($productDTO->description as $locale => $value) {
            $product->setTranslation('description', $locale, $value);
        }

        // Fill the product with the remaining data from the DTO
        $product->fill($productDTO->toArrayExceptTranslatable());

        // save the new version in a variable before returning otherwise it will return the old version
        $product = $product->saveWithVersion();

        return $product;

    }

    public function destroy(Product $product): bool
    {
        return $product->deleteWithoutVersion();
    }

    private function createProductDTO(CreateProductRequest|UpdateProductRequest $request, ?Product $product = null): ProductDTO
    {
        $isUpdate = $request instanceof UpdateProductRequest;

        return new ProductDTO(
            HelperService::getValueFromNullableCheck($request, $product, 'name', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $product, 'description', $isUpdate, []),
            HelperService::getValueFromNullableCheck($request, $product, 'image', $isUpdate, null),
            HelperService::Slugify(
                HelperService::getValueFromNullableCheck($request, $product, 'slug', $isUpdate, HelperService::getFallbackSlugAttributeFromTranslatable($request->name)),
                'slug',
                new Product,
                $isUpdate ? $product->version_identifier : null
            ),
            HelperService::rankify(new Product, HelperService::getValueFromNullableCheck($request, $product, 'rank', $isUpdate, null)),
            HelperService::getValueFromNullableCheck($request, $product, 'available', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $product, 'takeaway', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $product, 'delivery', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $product, 'eat_in', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $product, 'offer', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $product, 'vat', $isUpdate),
        );
    }
}
