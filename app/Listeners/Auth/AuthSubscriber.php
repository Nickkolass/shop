<?php

namespace App\Listeners\Auth;

use App\Notifications\Auth\EmailVerificationNotificationQueue;
use App\Notifications\Auth\WelcomeNotification;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;

class AuthSubscriber
{

    public function __construct(private readonly AuthService $authService)
    {
    }

    public function handleRegistered(Registered $event): void
    {
        dispatch(new EmailVerificationNotificationQueue($event));
        dispatch(new WelcomeNotification($event));
    }

    public function handleLogin(Login $event): void
    {
        $this->authService->setUserToSession($event->user);
        $this->authService->setJwtCookieToResponse();
    }

    public function handleLogout(Logout $event): void
    {
        $this->authService->jwtInvalidate();
    }

    /**
     * @param Dispatcher $events
     * @return array<string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Registered::class => 'handleRegistered',
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
        ];
    }
}
