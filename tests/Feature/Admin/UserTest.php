<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Tests\Feature\Trait\PrepareForTestTrait;
use Tests\TestCase;

class UserTest extends TestCase
{

    use PrepareForTestTrait;

    /**@test */
    public function test_a_user_can_be_viewed_any_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('users.index');

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.user.index');
    }

    /**@test */
    public function test_a_user_can_be_created_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('users.create');

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.user.create');
    }

    /**@test */
    public function test_a_user_can_be_stored_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $data = User::factory()->raw();
        unset($data['card'], $data['postcode'], $data['address'], $data['password']);
        $route = route('users.store');
        $from = route('users.create');

        $this->from($from)->post($route, $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)
                ->from($from)
                ->post($route, $data)
                ->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)
            ->from($from)
            ->expectsEvents(Registered::class)
            ->post($route, $data)
            ->assertOk();
        $this->assertTrue(User::query()->where('email', $data['email'])->exists());
    }

    /**@test */
    public function test_a_user_can_be_viewed_with_premissions(): void
    {
        /** @var Collection<User> $users */
        $users = User::factory(2)->create();
        /** @var User $user */
        $user = $users->first();
        /** @var User $another_user */
        $another_user = $users->last();
        $categories[] = Category::factory()->create();
        View::share('categories', $categories);
        $route = route('users.show', $user->id);
        $another_route = route('users.show', $another_user->id);

        $this->get($another_route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($another_route)->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertViewIs('admin.user.show');
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($another_route)->assertViewIs('admin.user.show');
    }

    /**@test */
    public function test_a_user_can_be_edited_with_premissions(): void
    {
        /** @var Collection<User> $users */
        $users = User::factory(2)->create();
        /** @var User $user */
        $user = $users->first();
        /** @var User $another_user */
        $another_user = $users->last();
        $categories[] = Category::factory()->create();
        View::share('categories', $categories);
        $route = route('users.edit', $user->id);
        $another_route = route('users.edit', $another_user->id);

        $this->get($another_route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($another_route)->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertViewIs('admin.user.edit');
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($another_route)->assertViewIs('admin.user.edit');
    }

    /**@test */
    public function test_a_user_can_be_updated_with_premissions(): void
    {
        $users = User::factory(2)->create();
        /** @var User $user */
        $user = $users->first();
        /** @var User $another_user */
        $another_user = $users->last();
        View::share('categories', [Category::factory()->create()->toArray()]);
        $route = route('users.update', $user->id);
        $another_route = route('users.update', $another_user->id);

        $data = User::factory()->raw();
        $data['id'] = $user->id;
        unset($data['card'], $data['postcode'], $data['address'], $data['password'], $data['INN']);

        $this->patch($another_route, $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->patch($another_route, $data)->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $data['email'] = $i . 'wqaqw@mail.ru';
            $this->actingAs($user)->patch($route, $data);
            $this->assertEquals($user->refresh()->email, $data['email']);
        }

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $data['email'] = 0 . $user->email;
        $data['id'] = $another_user->id;

        $this->actingAs($user)->patch($another_route, $data)->assertOk();
        $this->assertEquals($another_user->refresh()->email, $data['email']);
    }

    /**@test */
    public function test_a_user_can_be_deleted_with_premissions(): void
    {
        Category::factory()->create();
        /** @var Collection<User> $users */
        $users = User::factory(5)->has(Product::factory())->create();
        /** @var User $user */
        $user = $users->first();
        /** @var User $another_user */
        $another_user = $users->pop();
        $another_route = route('users.destroy', $another_user->id);

        $this->delete($another_route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->delete($another_route)->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            /** @var User $deleted_user */
            $deleted_user = $users->pop();
            $deleted_user->role = $i;
            $this->actingAs($deleted_user)->delete(route('users.destroy', $deleted_user->id))->assertRedirectToRoute('users.index');
            $this->assertModelMissing($deleted_user)
                ->assertFalse($deleted_user->products()->exists());
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->delete($another_route)->assertRedirectToRoute('users.index');
        $this->assertModelMissing($another_user)
            ->assertFalse($another_user->products()->exists());
    }

    /**@test */
    public function test_a_user_password_can_be_edited_with_premissions(): void
    {
        /** @var Collection<User> $users */
        $users = User::factory(2)->create();
        /** @var User $user */
        $user = $users->first();
        /** @var User $another_user */
        $another_user = $users->last();
        View::share('categories', [Category::factory()->create()->toArray()]);
        $route = route('users.password.edit', $user->id);
        $another_route = route('users.password.edit', $another_user->id);

        $this->get($another_route)->assertNotFound();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($another_route)->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertViewIs('admin.user.password');
        }
    }

    /**@test */
    public function test_a_user_password_can_be_updated_with_premissions(): void
    {
        /** @var Collection<User> $users */
        $users = User::factory(2)->create();
        /** @var User $user */
        $user = $users->first();
        /** @var User $another_user */
        $another_user = $users->last();
        $another_user->update(['password' => Hash::make('1')]);
        $route = route('users.password.update', $user->id);
        $another_route = route('users.password.update', $another_user->id);

        $password = Hash::make(Str::random(5));
        $data = ['password' => '1', 'new_password' => $password, 'new_password_confirmation' => $password];

        $this->patch($another_route, $data)->assertNotFound();

        for ($i = 1; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->patch($another_route, $data)->assertForbidden();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            session(['user.role' => $i]);
            $user->update(['role' => $i, 'password' => Hash::make((string)$i)]);
            $password = Hash::make(Str::random(5));
            $data = ['password' => (string)$i, 'new_password' => $password, 'new_password_confirmation' => $password];
            $this->actingAs($user)->patch($route, $data)->assertRedirectToRoute('users.show', $user->id);
        }
    }
}
