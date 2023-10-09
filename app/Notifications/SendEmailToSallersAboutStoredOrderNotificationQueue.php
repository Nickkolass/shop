<?php

namespace App\Notifications;

use App\Events\OrderStored;
use App\Mail\MailOrderPerformerStore;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailToSallersAboutStoredOrderNotificationQueue implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     *
     * @param OrderStored $event
     * @return void
     */
    public function handle(OrderStored $event): void
    {
        User::query()
            ->whereIn('id', $event->orderPerformers->keys())
            ->pluck('email', 'id')
            ->each(function ($email, $saler_id) use ($event) {
                Mail::to($email)->send(new MailOrderPerformerStore($event->orderPerformers[$saler_id]));
            });
    }
}
