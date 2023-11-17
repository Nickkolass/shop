<?php

namespace App\Providers;

use App\Components\Disk\DiskClientInterface;
use App\Components\Disk\Yandexdisk\YandexDiskClient;
use App\Components\HttpClient\GuzzleClient;
use App\Components\HttpClient\HttpClientInterface;
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
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
        $this->app->bind(HttpClientInterface::class, GuzzleClient::class);
        $this->app->bind(DiskClientInterface::class, YandexDiskClient::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (isset($_SERVER['REQUEST_URI']) && !str_starts_with($_SERVER['REQUEST_URI'], '/admin')) {
            View::share('categories', cache()->rememberForever('categories', fn() => Category::all()->toArray()));
        }
    }
}
