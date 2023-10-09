<?php

use App\Http\Controllers\Client\API\APIOrderController;
use App\Http\Controllers\Client\API\APIProductController;
use App\Http\Controllers\Client\API\APIUserActiveController;
use App\Http\Controllers\Client\API\JWTAuthController;
use Illuminate\Support\Facades\Route;

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

Route::post('/cart', [APIProductController::class, 'cart']);
Route::prefix('/products')->controller(APIProductController::class)->group(function () {
    Route::post('/liked', 'liked')->middleware('jwt.auth');
    Route::post('/', 'index');
    Route::post('/{category:title}', 'filter')->name('back.api.products.filter');
    Route::post('/show/{productType}', 'show');
});

Route::prefix('/products')->controller(APIUserActiveController::class)->middleware('jwt.auth')->group(function () {
    Route::post('/liked/{productType}', 'likedToggle');
    Route::post('{product}/comment', 'commentStore');
});

Route::prefix('/orders')->middleware(['jwt.auth', 'verified'])->controller(APIOrderController::class)->group(function () {
    Route::post('/', 'index')->name('back.api.orders.index');
    Route::post('/store', 'store');
    Route::post('/{order}', 'show')->withTrashed();
    Route::patch('/{order}', 'update');
    Route::delete('/{order}', 'destroy');
});

Route::prefix('auth')->controller(JWTAuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('me', 'me');
});
