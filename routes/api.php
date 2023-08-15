<?php

use App\Http\Controllers\API\BackController;
use App\Http\Controllers\API\BackOrderController;
use App\Http\Controllers\API\AuthController;
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

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('me', 'me');
});

Route::post('/cart', [BackController::class, 'cart']);
Route::prefix('/products')->controller(BackController::class)->group(function () {
    Route::post('/', 'index');
    Route::post('/liked', 'likedProducts')->middleware('jwt.auth');
    Route::post('/{category:title}', 'productIndex');
    Route::post('/show/{productType}', 'productShow')->name('back.api.productType.show');
    Route::post('/liked/{productType}', 'likedToggle')->middleware('jwt.auth');
    Route::post('/{product}/comment', 'commentStore')->middleware('jwt.auth');
});

Route::prefix('/orders')->middleware('jwt.auth')->controller(BackOrderController::class)->group(function () {
    Route::post('/', 'index')->name('back.api.orders.index');
    Route::post('/store', 'store');
    Route::post('/{order}', 'show')->withTrashed();
    Route::patch('/{order}', 'update');
    Route::delete('/{order}', 'destroy');
});
