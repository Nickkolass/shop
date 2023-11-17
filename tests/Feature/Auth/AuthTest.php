<?php

namespace Auth;

use App\Models\Category;
use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotificationQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use Ramsey\Collection\Collection;
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

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertRedirectToRoute('home');
        }
    }

    /**@test */
    public function test_a_user_can_be_logged(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->update(['password' => Hash::make('1')]);

        $this->withoutExceptionHandling();

        $this->expectsEvents(Login::class)
            ->post('/login', ['email' => $user->email, 'password' => '1'])
            ->assertRedirectToRoute('home');
        $this->assertAuthenticated();
    }

    /**@test */
    public function test_a_user_can_be_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->update(['password' => Hash::make('1')]);

        $this->withoutExceptionHandling();

        $this->actingAs($user)
            ->expectsEvents(Logout::class)
            ->post(route('logout'))
            ->assertRedirectToRoute('home');
        $this->assertFalse($this->isAuthenticated());
    }

    /**@test */
    public function test_a_user_can_be_viewed_register_page_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $this->get(route('register'))->assertViewIs('auth.register');

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)
                ->get(route('register'))
                ->assertRedirectToRoute('home');
        }
    }

    /**@test */
    public function test_a_user_can_be_registered(): void
    {
        $data = User::factory()->raw();
        $data['password_confirmation'] = $data['password'];
        $route = '/register';

        $this->withoutExceptionHandling();

        $this->expectsEvents(Registered::class)
            ->post($route, $data)
            ->assertRedirectToRoute('home');
        $this->assertTrue(User::query()->where('email', $data['email'])->exists());
        $this->assertAuthenticated();
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

    /**@test */
    public function test_a_user_can_be_viewed_password_reset_page_with_premissions(): void
    {
        /** @var Collection<User> $users */
        $users = User::factory(2)->create();
        /** @var User $user */
        $user = $users->first();
        /** @var User $another_user */
        $another_user = $users->last();
        $route = route('users.password.edit', $user->id);
        $another_route = route('users.password.edit', $another_user->id);
        View::share('categories', [Category::factory()->create()->toArray()]);
        session(['user' => ['id' => 1, 'name' => '1']]);

        $this->get($another_route)->assertNotFound();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)
                ->get($another_route)
                ->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)
                ->get($route)
                ->assertViewIs('admin.user.password');
        }
    }
}
