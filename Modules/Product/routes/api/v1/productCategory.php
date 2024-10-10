<?php

use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\V1\ProductCategoryController;

Route::prefix('v1/product')->group(function () {

    Route::get('category', [ProductCategoryController::class, 'index'])->name('category.index');

    Route::get('category/{id}', [ProductCategoryController::class, 'show'])->name('category.show');

});

Route::prefix('v1/product')->group(function () {

    Route::middleware(['auth:api', Admin::class])->group(function () {

        Route::post('category', [ProductCategoryController::class, 'store'])->name('category.store');

        Route::put('category/{id}', [ProductCategoryController::class, 'update'])->name('category.update');

        Route::delete('category/{id}', [ProductCategoryController::class, 'destroy'])->name('category.delete');
    });
});
