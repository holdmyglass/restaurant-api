<?php

namespace Modules\Product\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Modules\Product\Http\Requests\V1\CreateProductOptionItemRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionItemRequest;
use Modules\Product\Services\V1\ProductOptionItemService;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Exceptions\InvalidVersionException;
use Modules\Shared\Helpers\ApiResponse;

class ProductOptionItemController
{
    public function __construct(
        private readonly ProductOptionItemService $productOptionItemService
    ) {}

    public function index(): JsonResponse
    {

        try {

            $res = $this->productOptionItemService->getitems();

            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function byOption(string $id): JsonResponse
    {

        try {

            $res = $this->productOptionItemService->getItemsByOption($id);

            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function store(CreateProductOptionItemRequest $request): JsonResponse
    {
        $res = $this->productOptionItemService->createItem($request);

        $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

        return $response->toJson();
        try {

            $res = $this->productOptionItemService->createItemn($request);

            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $res = $this->productOptionItemService->getItemsById($id);
            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (InvalidArgumentException $e) {

            $response = ApiResponse::error($e->getMessage(), 'error', ServerStatusCodeEnum::BAD_REQUEST);

            return $response->toJson();
        } catch (ModelNotFoundException $e) {
            $response = ApiResponse::error($e->getMessage(), 'error', ServerStatusCodeEnum::NOT_FOUND);

            return $response->toJson();
        } catch (\Throwable $th) {
            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function update(UpdateProductOptionItemRequest $request, string $id): JsonResponse
    {

        try {

            $res = $this->productOptionItemService->updateItem($request, $id);

            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (InvalidArgumentException $e) {

            $response = ApiResponse::error($e->getMessage(), 'error', ServerStatusCodeEnum::BAD_REQUEST);

            return $response->toJson();
        } catch (QueryException $e) {

            $response = ApiResponse::error($e->getMessage(), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        } catch (ModelNotFoundException $e) {
            $response = ApiResponse::error(__('shared::messages.error.model_not_found'), 'error', ServerStatusCodeEnum::NOT_FOUND);

            return $response->toJson();
        } catch (InvalidVersionException $e) {
            $response = ApiResponse::error($e->getMessage(), 'error', ServerStatusCodeEnum::UNPROCESSABLE_CONTENT);

            return $response->toJson();
        } catch (\Throwable $th) {
            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function destroy(string $id): JsonResponse
    {

        try {

            $res = $this->productOptionItemService->deleteItem($id);

            $response = ApiResponse::success(null, $res['message'], $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (InvalidArgumentException $e) {

            $response = ApiResponse::error($e->getMessage(), 'error', ServerStatusCodeEnum::BAD_REQUEST);

            return $response->toJson();
        } catch (ModelNotFoundException $e) {
            $response = ApiResponse::error($e->getMessage(), 'error', ServerStatusCodeEnum::NOT_FOUND);

            return $response->toJson();
        } catch (\Throwable $th) {
            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }

    }
}
