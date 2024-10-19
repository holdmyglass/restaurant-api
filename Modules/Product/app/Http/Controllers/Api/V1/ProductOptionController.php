<?php

namespace Modules\Product\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Modules\Product\Http\Requests\V1\CreateProductOptionRequest;
use Modules\Product\Http\Requests\V1\UpdateProductOptionRequest;
use Modules\Product\Services\V1\ProductOptionService;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Exceptions\InvalidVersionException;
use Modules\Shared\Helpers\ApiResponse;
use Modules\Shared\Http\Controllers\Controller;

final class ProductOptionController extends Controller
{
    public function __construct(
        private readonly ProductOptionService $productOptionService
    ) {}

    public function index(): JsonResponse
    {

        try {

            $res = $this->productOptionService->getOptions();

            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function store(CreateProductOptionRequest $request): JsonResponse
    {

        try {

            $res = $this->productOptionService->createOption($request);

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
            $res = $this->productOptionService->getOptionById($id);
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

    public function update(UpdateProductOptionRequest $request, string $id): JsonResponse
    {

        try {

            $res = $this->productOptionService->updateOption($request, $id);

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

            $res = $this->productOptionService->deleteOption($id);

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
