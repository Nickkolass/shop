<?php

use App\Http\Controllers\API\BackController;
use App\Http\Controllers\API\BackOrderController;
use Illuminate\Http\Request;
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


Route::controller(BackController::class)->group(function () {
    Route::post('/cart', 'cart');
    Route::post('/products', 'index');
    Route::post('/products/liked', 'liked');
    Route::post('/products/liked/{productType}/toggle', 'likedToggle');
    Route::post('/products/{product}/comment', 'commentStore');
    Route::post('/products/{category:title}', 'products');
    Route::post('/products/{category:title}/{productType}', 'product');
});

Route::controller(BackOrderController::class)->group(function () {
    Route::post('/orders', 'index');
    Route::post('/orders/store', 'store');
    Route::post('/orders/{order}', 'show')->withTrashed();
    Route::patch('/orders/{order}', 'update');
    Route::delete('/orders/{order}', 'destroy');
});
