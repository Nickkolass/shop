<?php

namespace App\Providers;

use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderPerformerCanceled;
use App\Events\Order\OrderPerformerStored;
use App\Events\Order\OrderStored;
use App\Events\Order\Payment;
use App\Listeners\Payment\PaymentListener;
use App\Listeners\Payment\RefundOrderListener;
use App\Listeners\Payment\RefundOrderPerformerListener;
use App\Notifications\Auth\EmailVerificationNotificationQueue;
use App\Notifications\Auth\WelcomeNotification;
use App\Notifications\Order\OrderCanceledNotificationQueue;
use App\Notifications\Order\OrderPerformerCanceledNotificationQueue;
use App\Notifications\Order\OrderPerformerStoredNotificationQueue;
use App\Notifications\Order\OrderStoredNotificationQueue;
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
        OrderStored::class => [
            OrderStoredNotificationQueue::class,
        ],
        OrderPerformerStored::class => [
            OrderPerformerStoredNotificationQueue::class,
        ],
        OrderCanceled::class => [
            OrderCanceledNotificationQueue::class,
            RefundOrderListener::class,
        ],
        OrderPerformerCanceled::class => [
            OrderPerformerCanceledNotificationQueue::class,
            RefundOrderPerformerListener::class,
        ],
        Payment::class => [
            PaymentListener::class,
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
