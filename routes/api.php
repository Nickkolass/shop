<?php

use App\Http\Controllers\API\Order\StoreController;
use App\Http\Controllers\API\Product\ApiIndexController;
use App\Http\Controllers\API\Product\FilterListController;
use App\Http\Controllers\API\Product\ShowController;
use App\Http\Controllers\GetController;
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

Route::middleware(['auth:guest'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware('auth:guest')->group(function () {
        Route::get('/get', GetController::class);
    Route::post('/products', ApiIndexController::class);
    Route::post('/orders', StoreController::class);
    Route::get('/products/filters', FilterListController::class);
    Route::get('/products/{product}', ShowController::class);
});
