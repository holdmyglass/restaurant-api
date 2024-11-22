<?php

namespace Modules\Product\Repositories\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\DTO\ProductOptionDTO;
use Modules\Product\Enums\ProductOptionTypeEnum;
use Modules\Product\Http\Requests\V1\CreateProductOptionRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionRequest;
use Modules\Product\Interfaces\V1\ReadProductOptionRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductOptionRepositoryInterface;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductOption;
use Modules\Shared\Services\V1\HelperService;

class ProductOptionRepository implements ReadProductOptionRepositoryInterface, WriteProductOptionRepositoryInterface
{
    public function getAllOptions(): Collection
    {
        return ProductOption::all();
    }

    public function getOptionById(string $id): ProductOption
    {
        return ProductOption::findOrFail($id);
    }

    public function getProductOptionsByProductId(string $productId): Collection
    {
        $product = Product::findOrFail($productId);

        return $product->options()->get();
    }

    public function store(CreateProductOptionRequest $request): ProductOption
    {
        $productOptionDto = $this->createProductOptionDTO($request);

        $productOption = new ProductOption;

        // Set translations for name and description
        foreach ($productOptionDto->name as $locale => $value) {
            $productOption->setTranslation('name', $locale, $value);
        }

        foreach ($productOptionDto->description as $locale => $value) {
            $productOption->setTranslation('description', $locale, $value);
        }

        // Fill the product options with the remaining data from the DTO
        $productOption->fill($productOptionDto->toArrayExceptTranslatable());

        $productOption->save();

        return $productOption;
    }

    public function update(UpdateProductOptionRequest $request, ProductOption $productOption): ProductOption
    {
        $productOptionDto = $this->createProductOptionDTO($request, $productOption);

        // Set translations for name and description
        foreach ($request->name as $locale => $value) {
            $productOption->setTranslation('name', $locale, $value);
        }

        foreach ($request->description as $locale => $value) {
            $productOption->setTranslation('description', $locale, $value);
        }

        // Fill the product option with the remaining data from the DTO
        $productOption->fill($productOptionDto->toArrayExceptTranslatable());

        // save the new version in a variable before returning otherwise it will return the old version
        $product = $productOption->saveWithVersion();

        return $product;
    }

    public function destroy(ProductOption $productOption): bool
    {
        return $productOption->deleteWithoutVersion();
    }

    private function createProductOptionDTO(CreateProductOptionRequest|UpdateProductOptionRequest $request, ?ProductOption $productOption = null): ProductOptionDTO
    {
        $isUpdate = $request instanceof UpdateProductOptionRequest;

        return new ProductOptionDTO(
            HelperService::getValueFromNullableCheck($request, $productOption, 'name', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $productOption, 'description', $isUpdate, []),
            HelperService::Slugify(
                HelperService::getValueFromNullableCheck($request, $productOption, 'slug', $isUpdate, HelperService::getFallbackSlugAttributeFromTranslatable($request->name)),
                'slug',
                new ProductOption,
                $isUpdate ? $productOption->version_identifier : null
            ),
            ProductOptionTypeEnum::from(HelperService::getValueFromNullableCheck($request, $productOption, 'type', $isUpdate)),
            HelperService::getValueFromNullableCheck($request, $productOption, 'min', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $productOption, 'max', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $productOption, 'active', $isUpdate)
        );
    }
}
