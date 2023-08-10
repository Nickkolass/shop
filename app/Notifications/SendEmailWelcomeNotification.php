<?php

namespace App\Notifications;

use App\Mail\MailRegistered;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendEmailWelcomeNotification
{

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        Mail::to($event->user->email)->send(new MailRegistered($event->user->password_generated ?? null));
    }
}
