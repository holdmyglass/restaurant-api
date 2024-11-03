<?php

namespace Modules\Product\Services\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Product\Http\Requests\V1\CreateProductOptionItemRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionItemRequest;
use Modules\Product\Interfaces\V1\ReadProductOptionItemRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductOptionItemRepositoryInterface;
use Modules\Product\Models\ProductOptionItem;
use Modules\Product\Transformers\V1\ProductOptionItemCollection;
use Modules\Product\Transformers\V1\ProductOptionItemResource;
use Modules\Shared\Enums\ServerStatusCodeEnum;

class ProductOptionItemService
{
    public function __construct(
        private readonly ReadProductOptionItemRepositoryInterface $readProductOptionItem,
        private readonly WriteProductOptionItemRepositoryInterface $writeProductOptionItem,
    ) {}

    /**
     * Return all the current options items
     *
     * @return array<string, mixed>
     */
    public function getitems(): array
    {
        $items = $this->readProductOptionItem->getAllItems();

        $current_items = $items->filter(function ($option) {
            return $option->is_current_version;
        });

        return
        [
            'data' => [
                'product_option_item' => new ProductOptionItemCollection($current_items),
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
        $options = $this->readProductOptionItem->getAllItems();

        return
        [
            'data' => [
                'product_option_item' => new ProductOptionItemCollection($options),
                'count' => count($options),
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
    public function createItemn(CreateProductOptionItemRequest $request): array
    {

        $option = $this->writeProductOptionItem->store($request);

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
     * Update current item for option
     *
     * @return array<string, mixed>
     */
    public function updateItem(UpdateProductOptionItemRequest $request, string $id): array
    {

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
