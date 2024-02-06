<?php

namespace App\Providers;

use App\Components\Disk\DiskClientInterface;
use App\Components\Disk\YandexDiskClient;
use App\Components\Transport\TransportServiceProvider;
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
        $this->app->register(TransportServiceProvider::class);
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

    public function provides(): array
    {
        return [
            TransportServiceProvider::class,
            DiskClientInterface::class,
        ];
    }

}
