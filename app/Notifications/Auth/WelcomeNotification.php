<?php

namespace App\Notifications\Auth;

use App\Mail\MailWelcomeQueue;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class WelcomeNotification
{

    public function handle(Registered $event): void
    {
        Mail::to($event->user)->send(new MailWelcomeQueue($event->user->password_generated ?? null));
    }
}
