<?php

namespace Modules\Product\Repositories\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\DTO\ProductOptionItemDTO;
use Modules\Product\Http\Requests\V1\CreateProductOptionItemRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionItemRequest;
use Modules\Product\Interfaces\V1\ReadProductOptionItemRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductOptionItemRepositoryInterface;
use Modules\Product\Models\ProductOption;
use Modules\Product\Models\ProductOptionItem;
use Modules\Shared\Services\V1\HelperService;

class ProductOptionItemRepository implements ReadProductOptionItemRepositoryInterface, WriteProductOptionItemRepositoryInterface
{
    public function getAllItems(): Collection
    {
        return ProductOptionItem::all();
    }

    public function getItemById(string $id): ProductOptionItem
    {
        return ProductOptionItem::findOrFail($id);
    }

    public function getItemByOptionId(string $optionId): Collection
    {
        $option = ProductOption::findOrFail($optionId);

        return $option->items()->get();
    }

    public function store(CreateProductOptionItemRequest $request): ProductOptionItem
    {
        $productOptionItemDto = $this->createProductOptionDTO($request);

        $productOptionItem = new ProductOptionItem;

        // Set translations for name and description
        foreach ($request->name as $locale => $value) {
            $productOptionItem->setTranslation('name', $locale, $value);
        }

        foreach ($request->description as $locale => $value) {
            $productOptionItem->setTranslation('description', $locale, $value);
        }

        // Fill the product options with the remaining data from the DTO
        $productOptionItem->fill($productOptionItemDto->toArrayExceptTranslatable());

        $productOptionItem->save();

        return $productOptionItem;
    }

    public function update(UpdateProductOptionItemRequest $request, ProductOptionItem $productOptionItem): ProductOptionItem
    {
        $productOptionItemDto = $this->createProductOptionDTO($request, $productOptionItem);

        // Set translations for name and description
        foreach ($request->name as $locale => $value) {
            $productOptionItem->setTranslation('name', $locale, $value);
        }

        foreach ($request->description as $locale => $value) {
            $productOptionItem->setTranslation('description', $locale, $value);
        }

        // Fill the product option with the remaining data from the DTO
        $productOptionItem->fill($productOptionItemDto->toArrayExceptTranslatable());

        // save the new version in a variable before returning otherwise it will return the old version
        $product = $productOptionItem->saveWithVersion();

        return $product;
    }

    public function destroy(ProductOptionItem $productOptionItem): bool
    {
        return $productOptionItem->deleteWithoutVersion();
    }

    private function createProductOptionDTO(CreateProductOptionItemRequest|UpdateProductOptionItemRequest $request, ?ProductOptionItem $productOptionItem = null): ProductOptionItemDTO
    {
        $isUpdate = $request instanceof UpdateProductOptionItemRequest;

        return new ProductOptionItemDTO(
            HelperService::getValueFromNullableCheck($request, $productOptionItem, 'name', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $productOptionItem, 'description', $isUpdate, []),
            HelperService::Slugify(
                HelperService::getValueFromNullableCheck($request, $productOptionItem, 'slug', $isUpdate, HelperService::getFallbackSlugAttributeFromTranslatable($request->name)),
                'slug',
                new ProductOption,
                $isUpdate ? $productOptionItem->version_identifier : null
            ), HelperService::getValueFromNullableCheck($request, $productOptionItem, 'max', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $productOptionItem, 'max', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $productOptionItem, 'available', $isUpdate),
            HelperService::getValueFromNullableCheck($request, $productOptionItem, 'vat', $isUpdate),

        );
    }
}
