<?php

namespace Modules\Product\Services\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Product\Http\Requests\V1\CreateProductOptionRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionWithItemRequest;
use Modules\Product\Interfaces\V1\ReadProductOptionRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductOptionRepositoryInterface;
use Modules\Product\Models\ProductOption;
use Modules\Product\Transformers\V1\ProductOptionResource;
use Modules\Shared\Enums\ServerStatusCodeEnum;

class ProductOptionService
{
    public function __construct(
        private readonly ReadProductOptionRepositoryInterface $readProductOption,
        private readonly WriteProductOptionRepositoryInterface $writeProductOption,
    ) {}

    /**
     * Return all the current options
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        $options = $this->readProductOption->getAllOptions();

        $current_options = $options->filter(function ($option) {
            return $option->is_current_version;
        });

        return
        [
            'data' => [
                'product_options' => ProductOptionResource::collection($current_options),
                'count' => count($current_options),
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
    public function getOptionById(string $id): array
    {
        $productOption = $this->getProductOptionFromId($id);
        $productOptionResource = new ProductOptionResource($productOption);

        return [
            'data' => $productOptionResource->toArray(request()), // Convert to array
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Return all options including all older versions
     *
     * @return array<string, mixed>
     */
    public function getAllOptions(): array
    {
        $options = $this->readProductOption->getAllOptions();

        return
        [
            'data' => [
                'product_options' => ProductOptionResource::collection($options),
                'count' => count($options),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Create a new option
     *
     * @return array<string, mixed>
     */
    public function createOption(CreateProductOptionRequest $request): array
    {

        $option = $this->writeProductOption->store($request);

        return
        [
            'data' => [
                'product_option' => new ProductOptionResource($option),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Update current option
     *
     * @return array<string, mixed>
     */
    public function updateOption(UpdateProductOptionRequest $request, string $id): array
    {
        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        $productOption = $this->getProductOptionFromId($id);

        $option = $this->writeProductOption->update($request, $productOption);

        return
        [
            'data' => [
                'product_option' => new ProductOptionResource($option),
            ],
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
        ];
    }

    /**
     * Update current option
     *
     * @return array<string, mixed>
     */
    public function updateOptionItems(UpdateProductOptionWithItemRequest $request, string $id): array
    {
        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(message: __('shared::messages.error.invalid_resource_identifier'));
        }

        $productOption = $this->getProductOptionFromId($id);

        $productOption->items()->sync($request->item);

        return
        [
            'data' => [
                // 'product_option' => new ProductOptionItemsResource($option),
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
    public function deleteOption(string $id): array
    {
        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        $this->writeProductOption->destroy($this->getProductOptionFromId($id));

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
    private function getProductOptionFromId(string $id): ProductOption|InvalidArgumentException
    {

        if (! Str::isUuid($id)) {
            throw new InvalidArgumentException(__('shared::messages.error.invalid_resource_identifier'));
        }

        try {
            $product = $this->readProductOption->getOptionById($id);

            if ($product->is_deleted) {
                throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
            }

            return $product;
        } catch (ModelNotFoundException $e) {
            throw new InvalidArgumentException(__('shared::messages.error.model_not_found'));
        }

    }
}
