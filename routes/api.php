<?php

use App\Http\Controllers\API\Order\StoreController;
use App\Http\Controllers\API\Product\ApiIndexController;
use App\Http\Controllers\API\Product\FilterListController;
use App\Http\Controllers\API\Product\ShowController;
use App\Http\Controllers\Client\ClientIndexController;
use App\Http\Controllers\Client\ClientProductController;
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

Route::middleware(['guest'])->get('/user', function (Request $request) {
    return $request->user();
});


    Route::get('/', [ClientIndexController::class, 'index'])->name('client.index_client')->middleware('guest');
   
    Route::controller(ClientIndexController::class)->prefix('products')->middleware('guest')->group(function () {
        Route::get('/about', 'about')->name('client.about_client');
        Route::get('/contscts', 'cart')->name('client.cart_client');
        Route::get('/{category}', 'products')->name('client.product_client');
        Route::post('/{category}', 'products')->name('client.filter_client');
        Route::get('/{category}/{product}', 'show')->name('client.show_client');
    });
    Цвета должны соответствовать группам: продукты по цветам должны входить в одну группу 