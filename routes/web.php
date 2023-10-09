<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\OrderPerformerController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Product\ProductCreateController;
use App\Http\Controllers\Admin\Product\ProductEditController;
use App\Http\Controllers\Admin\Product\ProductTypeController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\Front\FrontOrderController;
use App\Http\Controllers\Client\Front\FrontProductController;
use App\Http\Controllers\Client\Front\FrontUserActiveController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('/', HomeController::class)->name('home');

Route::get('/users/{user}/password', [UserController::class, 'passwordEdit'])->name('users.password.edit');
Route::patch('/users/{user}/password', [UserController::class, 'passwordUpdate'])->name('users.password.update');
Route::resource('/users', UserController::class);

Route::name('client.')->group(function () {
    Route::view('/about', 'client.about')->name('about');
    Route::post('/orders/create', [FrontOrderController::class, 'create'])->name('orders.create')->middleware('client');
    Route::apiResource('/orders', FrontOrderController::class)->middleware('verified');
    Route::controller(FrontUserActiveController::class)->group(function () {
        Route::post('/cart', 'addToCart')->name('addToCart');
        Route::post('/products/liked/{productType}', 'likedToggle')->name('liked.toggle')->middleware('client');
        Route::post('/products/{product}/comment', 'commentStore')->name('comment.store')->middleware('client');
    });
    Route::get('/cart', [FrontProductController::class, 'cart'])->name('cart');
    Route::prefix('/products')->name('products.')->controller(FrontProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/show/{productType}', 'show')->name('show');
        Route::get('/liked', 'liked')->name('liked')->middleware('client');
        Route::match(['get', 'post'], '/{category}', 'filter')->name('filter');
    });
});

Route::prefix('/admin')->name('admin.')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::resources([
            'categories' => CategoryController::class,
            'tags' => TagController::class,
            'options' => OptionController::class,
            'properties' => PropertyController::class,
        ]);
    });
    Route::middleware('saler')->group(function () {
        Route::get('/', IndexController::class)->name('index');
        Route::apiResource('orders', OrderPerformerController::class)->withTrashed()->except('store');
        Route::prefix('/products')->name('products.')->group(function () {
            Route::get('/create', [ProductCreateController::class, 'index'])->name('create');
            Route::get('/create/relations', [ProductCreateController::class, 'relations'])->name('create.relations')->middleware('csrf');
            Route::get('/create/types', [ProductCreateController::class, 'types'])->name('create.types')->middleware('csrf');
            Route::get('/{product}/edit', [ProductEditController::class, 'index'])->name('edit');
            Route::get('/{product}/edit/relations', [ProductEditController::class, 'relations'])->name('edit.relations')->middleware('csrf');
            Route::patch('/{product}/publish', [ProductController::class, 'publish'])->name('publish');
        });
        Route::patch('/productTypes/{productType}/publish', [ProductTypeController::class, 'publish'])->name('productTypes.publish');
        Route::resource('products.productTypes', ProductTypeController::class)->names('productTypes')
            ->except(['index', 'show'])->shallow();
        Route::apiResource('products', ProductController::class);
    });
});
