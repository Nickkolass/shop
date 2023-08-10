<?php

namespace App\Providers;

use App\Events\OrderStored;
use App\Notifications\SendEmailToSallersAboutStoredOrderNotificationQueue;
use App\Notifications\SendEmailVerificationNotificationQueue;
use App\Notifications\SendEmailWelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotificationQueue::class,
            SendEmailWelcomeNotification::class,
        ],
        OrderStored::class => [
            SendEmailToSallersAboutStoredOrderNotificationQueue::class,
        ],
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
