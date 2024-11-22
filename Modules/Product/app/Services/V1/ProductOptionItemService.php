<?php

namespace Modules\Product\Services\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Product\Http\Requests\V1\CreateProductOptionItemRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionItemRequest;
use Modules\Product\Interfaces\V1\ReadProductOptionItemRepositoryInterface;
use Modules\Product\Interfaces\V1\WritePriceRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductOptionItemRepositoryInterface;
use Modules\Product\Models\ProductOptionItem;
use Modules\Product\Transformers\V1\ProductOptionItemResource;
use Modules\Shared\Enums\ServerStatusCodeEnum;

class ProductOptionItemService
{
    public function __construct(
        private readonly ReadProductOptionItemRepositoryInterface $readProductOptionItem,
        private readonly WriteProductOptionItemRepositoryInterface $writeProductOptionItem,
        private readonly WritePriceRepositoryInterface $writePrice,
    ) {}

    /**
     * Return all the current options items
     *
     * @return array<string, mixed>
     */
    public function getItems(): array
    {
        $items = $this->readProductOptionItem->getAllItems();

        $current_items = $items->filter(function ($option) {
            return $option->is_current_version;
        });

        return
        [
            'data' => [
                'product_option_item' => ProductOptionItemResource::collection($current_items),
                'count' => count($current_items),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];

    }

    /**
     * Return all the current options items
     *
     * @return array<string, mixed>
     */
    public function getItemsByOption(string $id): array
    {

        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        $items = $this->readProductOptionItem->getItemByOptionId($id);

        $current_items = $items->filter(function ($option) {
            return $option->is_current_version;
        });

        return
        [
            'data' => [
                'product_option_item' => ProductOptionItemResource::collection($current_items),
                'count' => count($current_items),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];

    }

    /**
     * Return a option by id
     *
     * @return array<string, mixed>
     */
    public function getItemsById(string $id): array
    {
        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        return
        [
            'data' => [
                'product_option_item' => new ProductOptionItemResource($this->getProductOptionItemFromId($id)),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Return all options including all older versions
     *
     * @return array<string, mixed>
     */
    public function getAllItems(): array
    {
        $items = $this->readProductOptionItem->getAllItems();

        return
        [
            'data' => [
                'product_option_item' => ProductOptionItemResource::collection($items),
                'count' => count($items),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Create a new item for option
     *
     * @return array<string, mixed>
     */
    public function createItem(CreateProductOptionItemRequest $request): array
    {

        $itemRequest = $request->except(['price']);

        $item = $this->writeProductOptionItem->store(new CreateProductOptionItemRequest($itemRequest));

        if (! is_null($request->price)) {
            foreach ($request->price as $price) {
                if ($price !== null) {
                    $this->writePrice->store((object) $price, ProductOptionItem::class, $item->id);
                }
            }
        }

        return
        [
            'data' => [
                'product_option_item' => new ProductOptionItemResource($item),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Update current item for option
     *
     * @return array<string, mixed>
     */
    public function updateItem(UpdateProductOptionItemRequest $request, string $id): array
    {

        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        $productOption = $this->getProductOptionItemFromId($id);

        $option = $this->writeProductOptionItem->update($request, $productOption);

        return
        [
            'data' => [
                'product_option_item' => new ProductOptionItemResource($option),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Delete Option
     *
     * @return array<string, mixed>
     */
    public function deleteItem(string $id): array
    {
        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        $this->writeProductOptionItem->destroy($this->getProductOptionItemFromId($id));

        return
        [
            'status_code' => ServerStatusCodeEnum::NO_CONTENT,
            'status' => 'success',
            'message' => __('shared::messages.success.deleted_successfully'),
        ];
    }

    /**
     * Return a product by id
     */
    private function getProductOptionItemFromId(string $id): ProductOptionItem|InvalidArgumentException
    {

        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        try {
            $product = $this->readProductOptionItem->getItemById($id);

            if ($product->is_deleted) {
                throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
            }

            return $product;
        } catch (ModelNotFoundException $e) {
            throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
        }

    }
}
