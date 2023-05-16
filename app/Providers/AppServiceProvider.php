<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Cache::forget('categories', Category::select('id', 'title', 'title_rus')->get()->toArray());
        Cache::has('categories') ?: Cache::forever('categories', Category::select('id', 'title', 'title_rus')->get()->toArray());

        if (isset($_SERVER['REQUEST_URI'])) {
            explode('/', $_SERVER['REQUEST_URI'])['1'] == 'admin' ?: View::share('categories', Cache::get('categories'));
        }
    }
}
