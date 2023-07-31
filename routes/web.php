<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\API\FrontController;
use App\Http\Controllers\API\FrontOrderController;

use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\OrderPerformerController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Product\ProductTypeController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Product\ProductCreateController;
use App\Http\Controllers\Admin\Product\ProductEditController;

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
Route::resource('/users', UserController::class);
Route::view('/users/{user}/password', 'user.password')->middleware('client')->name('users.password.edit');
Route::patch('/users/{user}/password', [UserController::class, 'password'])->middleware('client')->name('users.password.update');

Route::name('api.')->group(function () {
    Route::view('/about', 'api.about')->name('about');
    Route::post('/orders/create', [FrontOrderController::class, 'create'])->middleware('client')->name('orders.create');
    Route::apiResource('/orders', FrontOrderController::class)->middleware('client');
    Route::controller(FrontController::class)->group(function () {
        Route::get('/cart', 'cart')->name('cart');
        Route::post('/cart', 'addToCart')->name('addToCart');
        Route::prefix('/products')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/liked', 'liked')->name('liked')->middleware('client');
            Route::post('/liked/{productType}', 'likedToggle')->name('liked.toggle')->middleware('client');
            Route::get('/{category}', 'products')->name('products');
            Route::post('/{category}', 'products')->name('filter');
            Route::get('/show/{productType}', 'product')->name('product');
            Route::post('/{product}/comment', 'commentStore')->name('comment.store')->middleware('client');
        });
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
            Route::post('/create/properties', [ProductCreateController::class, 'properties'])->name('createProperties');
            Route::get('/create/types', [ProductCreateController::class, 'types'])->name('createTypes');
            Route::get('/{product}/edit', [ProductEditController::class, 'index'])->name('edit');
            Route::post('/{product}/edit/properties', [ProductEditController::class, 'properties'])->name('editProperties');
            Route::patch('/{product}/publish', [ProductController::class, 'publish'])->name('publish');
        });
        Route::patch('/products/publish/{productType}', [ProductTypeController::class, 'publish'])->name('productTypes.publish');
        Route::apiResource('products', ProductController::class);
        Route::resource('products.productTypes', ProductTypeController::class)->names('productTypes')
            ->except(['index', 'show'])->shallow();
    });
});
