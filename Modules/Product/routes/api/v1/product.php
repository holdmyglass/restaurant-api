<?php

use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\V1\ProductController;

Route::prefix('v1/product')->group(function () {

    Route::get('/list', [ProductController::class, 'list'])->name('product.list');

    Route::get('/', [ProductController::class, 'index'])->name('product.index');

    Route::get('/{id}', [ProductController::class, 'show'])->name('product.show');

});

Route::prefix('v1/product')->group(function () {

    Route::middleware(['auth:api', Admin::class])->group(function () {

        Route::post('/', [ProductController::class, 'store'])->name('product.store');

        Route::put('/{id}', [ProductController::class, 'update'])->name('product.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('product.delete');
    });
});
