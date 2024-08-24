<?php

use App\Http\Middleware\AuthenticateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\V1\Auth\LoginController;
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

Route::prefix('v1/auth')->group(function () {

    Route::middleware('guest')->group(function () {

        Route::post('register', [RegisterController::class, 'register'])->name('register');

        Route::post('verify-account', [RegisterController::class, 'verifyAccount'])->name('verify.account');

        Route::post('login', [LoginController::class, 'login'])->name('login');
    });

});

Route::prefix('v1')->group(function () {

    Route::middleware(['auth:api', AuthenticateProfile::class])->group(function () {

        Route::get('user', function (Request $request) {

            $current_profile = $request->input('profile');

            return response()->json($current_profile);
        });

        Route::post('auth/logout', [LoginController::class, 'logout'])->name('logout');
        Route::post('auth/logout-from-all-device', [LoginController::class, 'logoutFromAllDevice'])->name('logout.from.all.device');
    });
});
