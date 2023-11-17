<?php

namespace Auth;

use App\Mail\MailWelcomeQueue;
use App\Models\User;
use App\Notifications\Auth\EmailVerificationNotificationQueue;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class AuthEventTest extends TestCase
{

    /**@test */
    public function test_user_registered_event(): void
    {
        Mail::fake();
        Queue::fake();
        /** @var User $user */
        $user = User::factory()->create();
        $user->update(['email_verified_at' => null]);
        $this->withoutExceptionHandling();

        event(new Registered($user));
        Mail::assertQueued(MailWelcomeQueue::class);
        Queue::assertPushed(EmailVerificationNotificationQueue::class);
    }

    /**@test */
    public function test_user_login_event(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->withoutExceptionHandling();
        $this->partialMock(AuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setJwtCookieToResponse');
        });

        event(new Login('web', $user, false));
        $this->assertTrue(session()->exists('user'));
    }

    /**@test */
    public function test_user_logout_event(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->withoutExceptionHandling();
        $spy = $this->spy(AuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('jwtInvalidate');
        });

        event(new Logout('web', $user));
        $spy->shouldReceive('jwtInvalidate');
        $this->assertFalse($this->isAuthenticated());
    }
}
