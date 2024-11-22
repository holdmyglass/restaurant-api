<?php

namespace Modules\Product\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Modules\Product\Http\Requests\V1\CreateProductRequest;
use Modules\Product\Http\Requests\V1\UpdateProductRequest;
use Modules\Product\Services\V1\ProductService;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Exceptions\InvalidVersionException;
use Modules\Shared\Helpers\ApiResponse;
use Modules\Shared\Http\Controllers\Controller;

final class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        $res = $this->productService->getProducts();

        $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

        return $response->toJson();
        try {

            $res = $this->productService->getProducts();

            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function store(CreateProductRequest $request): JsonResponse
    {

        $res = $this->productService->createProduct($request);

        $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

        return $response->toJson();
        try {

            $res = $this->productService->createProduct($request);

            $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function show(string $id): JsonResponse
    {
        $res = $this->productService->getProductById($id);
        $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

        return $response->toJson();

        try {
            $res = $this->productService->getProductById($id);
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

    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {

        try {

            $res = $this->productService->updateProduct($request, $id);

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

        $res = $this->productService->deleteProduct($id);

        $response = ApiResponse::success(null, $res['message'], $res['status'], $res['status_code']);

        return $response->toJson();
        try {

            $res = $this->productService->deleteProduct($id);

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

    public function list(): JsonResponse
    {
        $res = $this->productService->getProductList();

        $response = ApiResponse::success($res['data'], null, $res['status'], $res['status_code']);

        return $response->toJson();
    }
}
