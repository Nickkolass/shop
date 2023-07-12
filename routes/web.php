<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\API\FrontController;
use App\Http\Controllers\API\FrontOrderController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Product\ProductTypeController;
use App\Http\Controllers\Admin\Product\ProductCreateController;
use App\Http\Controllers\Admin\Product\ProductDeleteController;
use App\Http\Controllers\Admin\Product\ProductEditController;
use App\Http\Controllers\Admin\Product\ProductIndexController;
use App\Http\Controllers\Admin\Product\ProductShowController;
use App\Http\Controllers\Admin\Product\ProductStoreController;
use App\Http\Controllers\Admin\Product\ProductUpdateController;

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

Route::name('api.')->group(function () {
    Route::view('/about', 'api.about')->name('about');
    Route::resource('/orders', FrontOrderController::class)->middleware('client')->except('edit');
    Route::controller(FrontController::class)->group(function () {
        Route::get('/support', 'support')->name('support')->middleware('client');
        Route::get('/cart', 'cart')->name('cart');
        Route::post('/cart', 'addToCart')->name('addToCart');
        Route::get('/products', 'index')->name('index');
        Route::get('/products/liked', 'liked')->name('liked')->middleware('client');
        Route::post('/products/liked/{productType}/toggle', 'likedToggle')->name('liked.toggle')->middleware('client');
        Route::get('/products/{category}', 'products')->name('products');
        Route::post('/products/{category}', 'products')->name('filter');
        Route::get('/products/{category}/{productType}', 'product')->name('product');
        Route::post('/products/{product}/comment', 'commentStore')->name('comment.store')->middleware('client');
    });
});

Route::prefix('/admin')->name('admin.')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::resource('/categories', CategoryController::class);
        Route::resource('/tags', TagController::class);
        Route::resource('/options', OptionController::class);
    });
    Route::middleware('saler')->group(function () {
        Route::view('/', 'admin.index')->name('index');
        Route::get('/support', [UserController::class, 'support'])->name('support');
        Route::resource('/orders', OrderController::class)->withTrashed()->only(['index', 'show', 'update', 'destroy']);
        Route::prefix('/products')->group(function () {
            Route::get('/', ProductIndexController::class)->name('products.index');
            Route::get('/create', [ProductCreateController::class, 'index'])->name('products.create');
            Route::post('/create/properties', [ProductCreateController::class, 'properties'])->name('products.createProperties');
            Route::get('/create/types', [ProductCreateController::class, 'types'])->name('products.createTypes');
            Route::post('/', ProductStoreController::class)->name('products.store');
            Route::get('/{product}', ProductShowController::class)->name('products.show');
            Route::get('/{product}/edit', [ProductEditController::class, 'index'])->name('products.edit');
            Route::post('/{product}/edit/properties', [ProductEditController::class, 'properties'])->name('products.editProperties');
            Route::patch('/{product}/publish', [ProductEditController::class, 'publish'])->name('products.publish');
            Route::patch('/{product}', ProductUpdateController::class)->name('products.update');
            Route::delete('/{product}', ProductDeleteController::class)->name('products.destroy');

            Route::controller(ProductTypeController::class)->group(function () {
                Route::get('{product}/types/create/', 'create')->name('productTypes.create');
                Route::post('/{product}/types', 'store')->name('productTypes.store');
                Route::get('/types/{productType}/edit', 'edit')->name('productTypes.edit');
                Route::patch('/types/{productType}', 'update')->name('productTypes.update');
                Route::delete('/types/{productType}', 'destroy')->name('productTypes.destroy');
                Route::patch('/types/publish/{productType}', 'publish')->name('productTypes.publish');
            });
        });
    });
});
