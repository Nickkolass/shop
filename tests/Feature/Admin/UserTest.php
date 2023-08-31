<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_a_user_can_be_viewed_any_with_premissions()
    {
        $user = User::factory()->create();

        $this->get(route('users.index'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.index'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('users.index'))->assertViewIs('admin.user.index');
        session()->flush();
    }

    /**@test */
    public function test_a_user_can_be_created_with_premissions()
    {
        $user = User::factory()->create();

        $this->get(route('users.create'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.create'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('users.create'))->assertViewIs('admin.user.create');
    }

    /**@test */
    public function test_a_user_can_be_stored_with_premissions()
    {
        $user = User::factory()->create();
        $data = User::factory()->raw();
        unset($data['card'], $data['postcode'], $data['address'], $data['password']);

        $this->from(route('users.create'))->post(route('users.store'), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->from(route('users.create'))->post(route('users.store'), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->from(route('users.create'))->post(route('users.store'), $data);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('jobs', 2);

        $user = User::first()->toArray();
        unset($user['id'], $user['email_verified_at'], $user['created_at'], $user['updated_at'], $data['email_verified_at'], $data['password']);

        $this->assertEquals(sort($data), sort($user));
    }

    /**@test */
    public function test_a_user_can_be_viewed_with_premissions()
    {
        $categories[] = Category::create(['title' => 'asf', 'title_rus' => 'asff']);
        View::share('categories', $categories);

        $users = User::factory(2)->create();
        $user = $users->first();
        $another_user = $users->last();

        $this->get(route('users.show', $another_user->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.show', $another_user->id))->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.show', $user->id))->assertViewIs('admin.user.show');
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('users.show', $another_user->id))->assertViewIs('admin.user.show');
    }

    /**@test */
    public function test_a_user_can_be_edited_with_premissions()
    {
        $categories[] = Category::create(['title' => 'asf', 'title_rus' => 'asff'])->toArray();
        View::share('categories', $categories);

        $users = User::factory(2)->create();
        $user = $users->first();
        $another_user = $users->last();

        $this->get(route('users.edit', $another_user->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.edit', $another_user->id))->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.edit', $user->id))->assertViewIs('admin.user.edit');
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('users.edit', $another_user->id))->assertViewIs('admin.user.edit');
    }

    /**@test */
    public function test_a_user_can_be_updated_with_premissions()
    {
        $categories[] = Category::create(['title' => 'ads', 'title_rus' => 'dgsog'])->toArray();
        View::share('categories', $categories);
        $users = User::factory(2)->create();
        $user = $users->first();
        $another_user = $users->last();

        $data = User::factory()->raw();
        $data['id'] = $user->id;
        unset($data['card'], $data['postcode'], $data['address']);

        $this->patch(route('users.update', $another_user->id), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch(route('users.update', $another_user->id), $data)->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $data_for_upd = $data;

            $this->actingAs($user)->patch(route('users.update', $user->id), $data_for_upd);

            $updated_user = User::first()->toArray();
            unset($updated_user['password'], $updated_user['email_verified_at'], $updated_user['gender'], $updated_user['card'], $updated_user['created_at'], $updated_user['updated_at'],
                $data_for_upd['password'], $data_for_upd['email_verified_at'], $data_for_upd['gender'], $data_for_upd['card']);
            $this->assertEquals(sort($data_for_upd), sort($updated_user));
            session()->flush();
        }

        $user->role = 1;
        $user->save();
        $data_for_upd = $data;
        $data_for_upd['id'] = $another_user->id;
        $data_for_upd['email'] = $another_user->email;
        $data_for_upd['INN'] = $another_user->INN;

        $this->actingAs($user)->patch(route('users.update', $another_user->id), $data_for_upd)->assertOk();

        $updated_user = User::latest()->first()->toArray();
        unset($updated_user['password'], $updated_user['email_verified_at'], $updated_user['gender'], $updated_user['card'], $updated_user['created_at'], $updated_user['updated_at'],
            $data_for_upd['password'], $data_for_upd['email_verified_at'], $data_for_upd['gender'], $data_for_upd['card']);
        $this->assertEquals(sort($data_for_upd), sort($updated_user));
    }

    /**@test */
    public function test_a_user_can_be_deleted_with_premissions()
    {
        Category::create(['title' => 'assdg', 'title_rus' => 'asdasd']);
        $users = User::factory(5)->has(
            Product::factory()
        )->create();
        $user = $users->first();
        $another_user = $users->last();

        $this->delete(route('users.destroy', $another_user->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->delete(route('users.destroy', $another_user->id))->assertForbidden();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $deleted_user = $users->pop();
            $deleted_user->role = $i;
            $this->actingAs($deleted_user)->delete(route('users.destroy', $deleted_user->id))->assertRedirect('/users');
            session()->flush();
            $this->assertDatabaseCount('users', 5 - $i);
            $this->assertTrue($deleted_user->products()->count() == 0);
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->delete(route('users.destroy', $users->last()->id))->assertRedirect('/users');
        $this->assertDatabaseCount('users', 1);
        $this->assertTrue($deleted_user->products()->count() == 0);
    }

    /**@test */
    public function test_a_user_password_can_be_edited_with_premissions()
    {
        $categories[] = Category::create(['title' => 'ads', 'title_rus' => 'dgsog'])->toArray();
        View::share('categories', $categories);
        $users = User::factory(2)->create();
        $user = $users->first();
        $another_user = $users->last();

        $this->get(route('users.password.edit', $user->id))->assertNotFound();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.password.edit', $another_user->id))->assertUnauthorized();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('users.password.edit', $user->id))->assertViewIs('admin.user.password');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_user_password_can_be_updated_with_premissions()
    {
        $users = User::factory(2)->create();
        $user = $users->first();
        $another_user = $users->last();
        $another_user->password = Hash::make(1);
        $another_user->save();

        $password = Hash::make(Str::random(5));
        $data = ['password' => '1', 'new_password' => $password, 'new_password_confirmation' => $password];

        $this->patch(route('users.password.update', $another_user->id), $data)->assertNotFound();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch(route('users.password.update', $another_user->id), $data)->assertUnauthorized();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->password = Hash::make($i);
            $user->save();
            $password = Hash::make(Str::random(5));
            $data = ['password' => (string)$i, 'new_password' => $password, 'new_password_confirmation' => $password];
            $this->actingAs($user)->patch(route('users.password.update', $user->id), $data)->assertRedirect('users/' . $user->id);
            session()->flush();
        }
    }
}
