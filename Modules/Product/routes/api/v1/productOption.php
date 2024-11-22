<?php

use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\V1\ProductOptionController;

Route::prefix('v1/product/option')->group(function () {

    Route::get('/', [ProductOptionController::class, 'index'])->name('product.option.index');
    Route::get('/{id}', [ProductOptionController::class, 'show'])->name('product.option.show');

});

Route::prefix('v1/product/option')->group(function () {

    Route::middleware(['auth:api', Admin::class])->group(function () {

        Route::post('/', [ProductOptionController::class, 'store'])->name('product.option.store');
        Route::put('/{id}', [ProductOptionController::class, 'update'])->name('product.option.update');
        Route::delete('/{id}', [ProductOptionController::class, 'destroy'])->name('product.option.delete');
        Route::put('/{id}/update-item', [ProductOptionController::class, 'updateItem'])->name('product.option.update.item');
    });
});
