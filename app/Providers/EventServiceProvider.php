<?php

namespace App\Providers;

use App\Listeners\Payment\PaymentSubscriber;
use App\Notifications\Auth\EmailVerificationNotificationQueue;
use App\Notifications\Auth\WelcomeNotification;
use App\Notifications\Order\OrderNotificationSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     */
    protected $listen = [
        Registered::class => [
            EmailVerificationNotificationQueue::class,
            WelcomeNotification::class,
        ],
    ];

    /**
     * @var array<string>
     */
    protected $subscribe = [
        OrderNotificationSubscriber::class,
        PaymentSubscriber::class,
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
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
