<?php

namespace App\Providers;

use App\Listeners\Auth\AuthSubscriber;
use App\Listeners\Client\API\PaymentSubscriber;
use App\Notifications\Order\OrderNotificationSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     */
    protected $listen = [
    ];

    /**
     * @var array<string>
     */
    protected $subscribe = [
        OrderNotificationSubscriber::class,
        PaymentSubscriber::class,
        AuthSubscriber::class
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
