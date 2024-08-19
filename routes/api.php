<?php

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

use Illuminate\Support\Facades\Route;

include __DIR__.'/api/v1/auth.php';

Route::get('/hello', function () {
    return 'Hello, World!';
});
