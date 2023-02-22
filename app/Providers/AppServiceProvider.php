<?php

namespace App\Providers;

use App\Models\Category;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            explode('/', $_SERVER['REQUEST_URI'])['1'] == 'admin'
                ? ''
                : View::share('categories', Category::select('id', 'title', 'title_rus')->get()->toArray());
        }
    }
}
