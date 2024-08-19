<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\V1\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    Route::middleware('guest')->group(function () {

        Route::post('auth/register', [RegisterController::class, 'register'])->name('register');
    });

    // Route::middleware(['auth:api', 'auth.profile'])->group(function () {

    //     // Route::get('/user', function (Request $request) {
    //     //     $authenticatedProfile = $request->input('authenticated_profile');
    //     //     return $authenticatedProfile;
    //     // });

    //     // Route::post('auth/logout', [LoginController::class, 'logout'])->name('logout');
    //     // Route::post('auth/logout-from-all-device', [LoginController::class, 'logoutFromAllDevice'])->name('logout.from.all.device');
    // });
});
