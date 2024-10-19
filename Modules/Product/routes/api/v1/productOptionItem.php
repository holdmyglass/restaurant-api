<?php

use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\V1\ProductOptionItemController;

Route::prefix('v1/product/option/item')->group(function () {

    Route::get('/', [ProductOptionItemController::class, 'index'])->name('product.option.item.index');
    Route::get('/{id}', [ProductOptionItemController::class, 'show'])->name('product.option.item.show');

});

Route::prefix('v1/product/option/item')->group(function () {

    Route::middleware(['auth:api', Admin::class])->group(function () {

        Route::post('/', [ProductOptionItemController::class, 'store'])->name('product.option.item.store');
        Route::put('/{id}', [ProductOptionItemController::class, 'update'])->name('product.option.item.update');
        Route::delete('/{id}', [ProductOptionItemController::class, 'destroy'])->name('product.option.item.delete');
    });
});
