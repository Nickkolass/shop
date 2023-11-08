<?php

use App\Http\Controllers\Client\API\APIOrderController;
use App\Http\Controllers\Client\API\APIProductController;
use App\Http\Controllers\Client\API\APIUserActiveController;
use App\Http\Controllers\Client\API\JWTAuthController;
use App\Http\Controllers\Payment\PaymentController;
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

Route::name('back.api.')->group(function () {
    Route::post('/cart', [APIProductController::class, 'cart'])->name('cart');
    Route::prefix('/products')->name('products.')->group(function () {
        Route::controller(APIProductController::class)->group(function () {
            Route::post('/liked', 'liked')->middleware('jwt.auth')->name('liked');
            Route::post('/', 'index')->name('index');
            Route::post('/{category:title}', 'filter')->name('filter');
            Route::post('/show/{productType}', 'show')->name('show');
        });
        Route::controller(APIUserActiveController::class)->middleware('jwt.auth')->group(function () {
            Route::post('/liked/{productType}', 'likedToggle')->name('likedToggle');
            Route::post('{product}/comment', 'commentStore')->middleware('verified')->name('commentStore');
        });
    });

    Route::prefix('/orders')->name('orders.')->middleware(['jwt.auth', 'verified'])->group(function () {
        Route::controller(PaymentController::class)->group(function () {
            Route::post('/{order}/payment', 'payment')->name('payment');
            Route::post('/{order}/refund', 'refund')->name('refund');
            Route::post('/payment/callback', 'callback')->withoutMiddleware(['jwt.auth', 'verified'])->name('callback');
        });
        Route::controller(APIOrderController::class)->group(function () {
            Route::post('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{order}', 'show')->withTrashed()->name('show');
            Route::patch('/{order}', 'update')->name('update');
            Route::delete('/{order}', 'destroy')->name('destroy');
            Route::delete('/delete/{orderPerformer}', 'destroyOrderPerformer')->name('destroyOrderPerformer');
        });
    });

    Route::prefix('auth')->name('auth.')->controller(JWTAuthController::class)->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::post('me', 'me')->name('me');
    });
});
