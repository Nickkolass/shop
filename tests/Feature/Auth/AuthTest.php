<?php

namespace Auth;

use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotificationQueue;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{

    /**@test */
    public function test_a_user_can_be_viewed_login_page_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('login');

        $this->withoutExceptionHandling();

        $this->get($route)->assertViewIs('auth.login');
        session()->flush();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)->assertRedirectToRoute('home');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_user_can_be_logged(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->password = Hash::make('1');
        $user->save();

        $this->withoutExceptionHandling();

        $this->post('/login', ['email' => $user->email, 'password' => '1'])->assertRedirectToRoute('home');
    }

    /**@test */
    public function test_a_user_can_be_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->post(route('logout'))->assertRedirectToRoute('home');
    }

    /**@test */
    public function test_a_user_can_be_viewed_register_page_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $this->get(route('register'))->assertViewIs('auth.register');

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('register'))->assertRedirectToRoute('home');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_user_can_be_registered(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $data = User::factory()->raw();
        $data['password_confirmation'] = $data['password'];
        $route = '/register';

        $this->withoutExceptionHandling();

        $this->expectsEvents(Registered::class)
            ->post($route, $data)
            ->assertRedirectToRoute('home');
        $this->assertTrue(User::query()->where('email', $data['email'])->exists());
        session()->flush();
    }

    /**@test */
    public function test_a_user_can_be_logged_using_remember_token(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->password = Hash::make('1');

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->remember_token = null;
            $user->save();

            $res = $this->post('/login', ['email' => $user->email, 'password' => '1', 'remember' => 'on']);

            $cookie_name = collect(session()->all())->filter(fn($v, $k) => str_starts_with($k, 'login_web'))->keys()->first();
            $cookie_name = str_replace('login_web', 'remember_web', (string)$cookie_name);
            $cookie = $res->getCookie($cookie_name);

            $this->withCookie($cookie_name, $cookie)->get(route('home'))->assertRedirectToRoute($i == User::ROLE_CLIENT ? 'client.products.index' : 'admin.index');
            $this->actingAs($user)->post(route('logout'));
            session()->flush();
        }
    }

    /**@test */
    public function test_a_password_can_be_reset(): void
    {
        Notification::fake();
        /** @var User $user */
        $user = User::factory()->create();
        $this->withoutExceptionHandling();
        $data = [
            '_token' => csrf_token(),
            'email' => $user->email,
        ];
        $this->post(route('password.email'), $data)->assertRedirect();
        Notification::assertSentTo($user, ResetPasswordNotificationQueue::class);
        $this->assertDatabaseHas('password_resets', ['email' => $user->email]);

        $this->post(route('password.email'), $data)->assertRedirect();
        Notification::assertCount(1);
        $this->assertDatabaseCount('password_resets', 1);
    }
}
