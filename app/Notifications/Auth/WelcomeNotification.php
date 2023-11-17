<?php

namespace App\Notifications\Auth;

use App\Mail\MailWelcomeQueue;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class WelcomeNotification
{

    public function __construct(public readonly Registered $event)
    {
    }

    public function handle(): void
    {
        Mail::to($this->event->user)->send(new MailWelcomeQueue($this->event->user->password_generated ?? null));
    }
}
