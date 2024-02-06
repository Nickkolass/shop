<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerificationNotificationQueue implements ShouldQueue
{

    public function __construct(public readonly Registered $event)
    {
    }

    public function handle(): void
    {
        if ($this->event->user instanceof MustVerifyEmail && !$this->event->user->hasVerifiedEmail()) {
            $this->event->user->sendEmailVerificationNotification();
        }
    }
}
