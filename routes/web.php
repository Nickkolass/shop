<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Client\ClientIndexController;



use App\Http\Controllers\Main\MainIndexController;

use App\Http\Controllers\Category\CategoryCreateController;
use App\Http\Controllers\Category\CategoryDeleteController;
use App\Http\Controllers\Category\CategoryEditController;
use App\Http\Controllers\Category\CategoryShowController;
use App\Http\Controllers\Category\CategoryStoreController;
use App\Http\Controllers\Category\CategoryUpdateController;
use App\Http\Controllers\Category\CategoryIndexController;

use App\Http\Controllers\Color\ColorCreateController;
use App\Http\Controllers\Color\ColorDeleteController;
use App\Http\Controllers\Color\ColorEditController;
use App\Http\Controllers\Color\ColorIndexController;
use App\Http\Controllers\Color\ColorShowController;
use App\Http\Controllers\Color\ColorStoreController;
use App\Http\Controllers\Color\ColorUpdateController;

use App\Http\Controllers\Tag\TagCreateController;
use App\Http\Controllers\Tag\TagDeleteController;
use App\Http\Controllers\Tag\TagEditController;
use App\Http\Controllers\Tag\TagIndexController;
use App\Http\Controllers\Tag\TagShowController;
use App\Http\Controllers\Tag\TagStoreController;
use App\Http\Controllers\Tag\TagUpdateController;

use App\Http\Controllers\User\UserCreateController;
use App\Http\Controllers\User\UserDeleteController;
use App\Http\Controllers\User\UserEditController;
use App\Http\Controllers\User\UserIndexController;
use App\Http\Controllers\User\UserShowController;
use App\Http\Controllers\User\UserStoreController;
use App\Http\Controllers\User\UserUpdateController;

use App\Http\Controllers\Product\ProductCreateController;
use App\Http\Controllers\Product\ProductDeleteController;
use App\Http\Controllers\Product\ProductEditController;
use App\Http\Controllers\Product\ProductIndexController;
use App\Http\Controllers\Product\ProductShowController;
use App\Http\Controllers\Product\ProductStoreController;
use App\Http\Controllers\Product\ProductUpdateController;

use App\Http\Controllers\Group\GroupCreateController;
use App\Http\Controllers\Group\GroupDeleteController;
use App\Http\Controllers\Group\GroupEditController;
use App\Http\Controllers\Group\GroupIndexController;
use App\Http\Controllers\Group\GroupShowController;
use App\Http\Controllers\Group\GroupStoreController;
use App\Http\Controllers\Group\GroupUpdateController;

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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::get('/homer', [ClientIndexController::class])->name('homer');


Route::group(['prefix' => 'admin', 'middleware' => 'saler'], function () {
    Route::get('/', MainIndexController::class)->name('main.index_main');
    Route::group(['prefix' => '/products'], function () {
        Route::get('/', ProductIndexController::class)->name('product.index_product');
        Route::get('/create', ProductCreateController::class)->name('product.create_product');
        Route::post('/', ProductStoreController::class)->name('product.store_product');
        Route::get('/{product}', ProductShowController::class)->name('product.show_product');
        Route::get('/{product}/edit', ProductEditController::class)->name('product.edit_product');
        Route::patch('/{product}', ProductUpdateController::class)->name('product.update_product');
        Route::delete('/{product}', ProductDeleteController::class)->name('product.delete_product');
    });
});

Route::group(['prefix' => 'users', 'middleware' => 'client'], function () {
    Route::get('/', UserIndexController::class)->name('user.index_user');
    Route::get('/create', UserCreateController::class)->name('user.create_user');
    Route::post('/', UserStoreController::class)->name('user.store_user');
    Route::get('/{user}', UserShowController::class)->name('user.show_user');
    Route::get('/{user}/edit', UserEditController::class)->name('user.edit_user');
    Route::patch('/{user}', UserUpdateController::class)->name('user.update_user');
    Route::delete('/{user}', UserDeleteController::class)->name('user.delete_user');
});

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', CategoryIndexController::class)->name('category.index_category');
        Route::get('/create', CategoryCreateController::class)->name('category.create_category');
        Route::post('/', CategoryStoreController::class)->name('category.store_category');
        Route::get('/{category}', CategoryShowController::class)->name('category.show_category');
        Route::get('/{category}/edit', CategoryEditController::class)->name('category.edit_category');
        Route::patch('/{category}', CategoryUpdateController::class)->name('category.update_category');
        Route::delete('/{category}', CategoryDeleteController::class)->name('category.delete_category');
    });

    Route::group(['prefix' => 'colors'], function () {
        Route::get('/', ColorIndexController::class)->name('color.index_color');
        Route::get('/create', ColorCreateController::class)->name('color.create_color');
        Route::post('/', ColorStoreController::class)->name('color.store_color');
        Route::get('/{color}', ColorShowController::class)->name('color.show_color');
        Route::get('/{color}/edit', ColorEditController::class)->name('color.edit_color');
        Route::patch('/{color}', ColorUpdateController::class)->name('color.update_color');
        Route::delete('/{color}', ColorDeleteController::class)->name('color.delete_color');
    });

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/', TagIndexController::class)->name('tag.index_tag');
        Route::get('/create', TagCreateController::class)->name('tag.create_tag');
        Route::post('/', TagStoreController::class)->name('tag.store_tag');
        Route::get('/{tag}', TagShowController::class)->name('tag.show_tag');
        Route::get('/{tag}/edit', TagEditController::class)->name('tag.edit_tag');
        Route::patch('/{tag}', TagUpdateController::class)->name('tag.update_tag');
        Route::delete('/{tag}', TagDeleteController::class)->name('tag.delete_tag');
    });

    Route::group(['prefix' => 'groups'], function () {
        Route::get('/', GroupIndexController::class)->name('group.index_group');
        Route::get('/create', GroupCreateController::class)->name('group.create_group');
        Route::post('/', GroupStoreController::class)->name('group.store_group');
        Route::get('/{group}', GroupShowController::class)->name('group.show_group');
        Route::get('/{group}/edit', GroupEditController::class)->name('group.edit_group');
        Route::patch('/{group}', GroupUpdateController::class)->name('group.update_group');
        Route::delete('/{group}', GroupDeleteController::class)->name('group.delete_group');
    });
});
