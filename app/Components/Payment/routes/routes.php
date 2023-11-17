<?php

use App\Components\Payment\src\Http\Controllers\PaymentController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::controller(PaymentController::class)->group(function () {

    Route::middleware(['web', 'role:' . User::ROLE_SALER])->group(function () {
        Route::post('/admin/orders/{order}/payout', 'payout')->name('admin.orders.payout');
        Route::prefix('/users/{user}')->name('users.')->group(function () {
            Route::get('/card', 'cardEdit')->name('card.edit');
            Route::patch('/card', 'cardUpdate')->name('card.update');
        });
    });

    Route::middleware('api')->prefix('/api/orders')->name('back.api.orders.')->group(function () {
        Route::post('/{order}/pay', 'pay')->middleware('jwt.auth')->name('pay');
        Route::post('/{order}/refund', 'refund')->middleware('jwt.auth')->withTrashed()->name('refund');
        Route::post('/payment/callback', 'callback')->name('callback');
    });
});
