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

Route::controller(BackController::class)->group(function () {
    Route::post('/cart', 'cart');
    Route::post('/products', 'index');
    Route::post('/products/{category:title}', 'products');
    Route::post('/products/show/{productType}', 'product');
    Route::post('/products/liked', 'liked')->middleware('jwt.auth');
    Route::post('/products/liked/{productType}/toggle', 'likedToggle')->middleware('jwt.auth');
    Route::post('/products/{product}/comment', 'commentStore')->middleware('jwt.auth');
});

Route::middleware('jwt.auth')->controller(BackOrderController::class)->group(function () {
    Route::post('/orders', 'index');
    Route::post('/orders/store', 'store');
    Route::post('/orders/{order}', 'show')->withTrashed();
    Route::patch('/orders/{order}', 'update');
    Route::delete('/orders/{order}', 'destroy');
});
