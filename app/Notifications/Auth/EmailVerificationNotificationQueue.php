<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerificationNotificationQueue extends SendEmailVerificationNotification implements ShouldQueue
{
    use Queueable;
}
