<?php

namespace App\Components\Payment\src;

use App\Components\Payment\src\Clients\PaymentClientInterface;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/payment.php', 'payment');

        $connection = config('payment.default');
        $bind = config("payment.connections.$connection.bind");
        $this->app->bind(PaymentClientInterface::class, $bind);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'payment');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [PaymentClientInterface::class];
    }
}
