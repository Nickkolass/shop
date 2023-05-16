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

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'api.about')->name('api.about_api');
Route::controller(FrontController::class)->group(function () {
    Route::get('/products', 'index')->name('api.index_api');
    Route::get('/products/{category}', 'products')->name('api.products_api');
    Route::post('/products/{category}', 'products')->name('api.filter_api');
    Route::get('/products/{category}/{productType}', 'product')->name('api.product_api');
    Route::post('/cart', 'addToCart')->name('api.addToCart_api');
    Route::get('/cart', 'cart')->name('api.cart_api');
    Route::get('/support', 'support')->name('api.support_api')->middleware('client');
});
Route::resource('/orders', FrontOrderController::class)->middleware('client')->except('edit')->withTrashed()->names([
    'index' => 'api.orders_api', 'create' => 'api.preOrdering_api',
    'store' => 'api.ordering_api', 'show' => 'api.orderShow_api',
    'update' => 'api.orderStatus_api', 'destroy' => 'api.orderDelete_api'
]);

Route::resource('/users', UserController::class)->names([
    //middleware в контроллере
    'index' => 'user.index_user', 'create' => 'user.create_user',
    'store' => 'user.store_user', 'show' => 'user.show_user',
    'edit' => 'user.edit_user', 'update' => 'user.update_user',
    'destroy' => 'user.delete_user'
]);

Route::prefix('/admin')->group(function () {
    Route::middleware('saler')->group(function () {
        Route::view('/', 'admin.index_admin')->name('admin.index_admin');
        Route::get('/support', [UserController::class, 'support'])->name('user.support_user');
        Route::prefix('/products')->group(function () {
            Route::get('/', ProductIndexController::class)->name('product.index_product');
            Route::get('/create', [ProductCreateController::class, 'index'])->name('product.create_product');
            Route::post('/create/properties', [ProductCreateController::class, 'properties'])->name('product.createProperties_product');
            Route::post('/create/types', [ProductCreateController::class, 'types'])->name('product.createTypes_product');
            Route::post('/', ProductStoreController::class)->name('product.store_product');
            Route::get('/{product}', ProductShowController::class)->name('product.show_product');
            Route::get('/{product}/edit', [ProductEditController::class, 'index'])->name('product.edit_product');
            Route::post('/{product}/edit/properties', [ProductEditController::class, 'properties'])->name('product.editProperties_product');
            Route::patch('/{product}', ProductUpdateController::class)->name('product.update_product');
            Route::delete('/{product}', ProductDeleteController::class)->name('product.delete_product');

            Route::get('{product}/types/create/', [ProductTypeController::class, 'create'])->name('productType.create_productType');
            Route::post('/{product}/types', [ProductTypeController::class, 'store'])->name('productType.store_productType');
            Route::prefix('/types')->group(function () {
                Route::get('/{productType}/edit', [ProductTypeController::class, 'edit'])->name('productType.edit_productType');
                Route::patch('/{productType}', [ProductTypeController::class, 'update'])->name('productType.update_productType');
                Route::delete('/{productType}', [ProductTypeController::class, 'destroy'])->name('productType.delete_productType');
                Route::patch('/published/{productType}', [ProductTypeController::class, 'published'])->name('productType.published_productType');
            });
        });

        Route::resource('/orders', OrderController::class)->withTrashed()
            ->only(['index', 'show', 'update', 'destroy'])
            ->names([
                'index' => 'order.index_order', 'show' => 'order.show_order',
                'update' => 'order.update_order', 'destroy' => 'order.delete_order'
            ]);
    });

    Route::middleware('admin')->group(function () {
        Route::resource('/categories', CategoryController::class)->names([
            'index' => 'category.index_category', 'create' => 'category.create_category',
            'store' => 'category.store_category', 'show' => 'category.show_category',
            'edit' => 'category.edit_category', 'update' => 'category.update_category',
            'destroy' => 'category.delete_category'
        ]);
        Route::resource('/tags', TagController::class)->names([
            'index' => 'tag.index_tag', 'create' => 'tag.create_tag',
            'store' => 'tag.store_tag', 'show' => 'tag.show_tag',
            'edit' => 'tag.edit_tag', 'update' => 'tag.update_tag',
            'destroy' => 'tag.delete_tag'
        ]);
        Route::resource('/options', OptionController::class)->names([
            'index' => 'option.index_option', 'create' => 'option.create_option',
            'store' => 'option.store_option', 'show' => 'option.show_option',
            'edit' => 'option.edit_option', 'update' => 'option.update_option',
            'destroy' => 'option.delete_option'
        ]);
    });
});
