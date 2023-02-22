<?php

use App\Http\Controllers\API\BackController;
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
    Route::post('/user', function (Request $request) {return $request->user();});
    Route::prefix('products')->group(function () {
        Route::post('/{category:title}', 'products');
        Route::post('/{category}/{product}', 'show');
    });
    Route::post('/cart', 'cart');
    Route::post('/ordering', 'ordering');
    Route::post('/orders', 'orders');
});

