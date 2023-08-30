<?php

namespace Client\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_a_user_can_be_viewed_login_page_with_premissions()
    {
        $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $this->get('/login')->assertViewIs('auth.login');
        session()->flush();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get('/login')->assertRedirect(route('home'));
            session()->flush();
        }
    }

    /**@test */
    public function test_a_user_can_be_logged()
    {
        $user = User::factory()->create();
        $user->password = Hash::make('1');
        $user->save();

        $this->withoutExceptionHandling();

        $this->post('/login', ['email' => $user->email, 'password' => '1'])->assertRedirect(route('home'));
    }

    /**@test */
    public function test_a_user_can_be_logout()
    {
        $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('home'));
    }

    /**@test */
    public function test_a_user_can_be_viewed_register_page_with_premissions()
    {
        $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $this->get(route('register'))->assertViewIs('auth.register');

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('register'))->assertRedirect(route('home'));
            session()->flush();
        }
    }

    /**@test */
    public function test_a_user_can_be_registered_page_with_premissions()
    {
        $user = User::factory()->create();
        $data = User::factory()->raw();
        $data['password_confirmation'] = $data['password'];

        $this->withoutExceptionHandling();

        $this->post('/register', $data)->assertRedirect(route('home'));
        $this->assertDatabaseCount('jobs', 2);
        session()->flush();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post('/register', $data)->assertRedirect(route('home'));
            $this->assertDatabaseCount('jobs', 2);
            session()->flush();
        }
    }

    /**@test */
    public function test_a_user_can_be_logged_using_remember_token()
    {
        $user = User::factory()->create();
        $user->password = Hash::make('1');

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->remember_token = null;
            $user->save();

            $res = $this->post('/login', ['email' => $user->email, 'password' => '1', 'remember' => 'on']);

            $cookie_name = collect(session()->all())->filter(fn($v, $k) => str_starts_with($k, 'login_web'))->keys()->first();
            $cookie_name = str_replace('login_web', 'remember_web', $cookie_name);
            $cookie = $res->getCookie($cookie_name);

            $this->withCookie($cookie_name, $cookie)->get(route('home'))->assertRedirect($i == 3 ? '/products' : '/admin');
            $this->actingAs($user)->post(route('logout'));
            session()->flush();
        }
    }
}
