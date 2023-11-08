<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    /**@test */
    public function test_a_category_can_be_viewed_any_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Category::query()->create(['title' => 'sadfsdf']);
        $route = route('admin.categories.index');
        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($route)->assertViewIs('admin.category.index');
    }

    /**@test */
    public function test_a_category_can_be_created_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $route = route('admin.categories.create');

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($route)->assertViewIs('admin.category.create');
    }

    /**@test */
    public function test_a_category_can_be_stored_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $data = ['title' => 'xcvxc', 'title_rus' => 'cvxcv'];
        $route = route('admin.categories.store');
        $this->post($route, $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post($route, $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->post($route, $data);
        $this->assertTrue(Category::query()->where('title', $data['title'])->exists());
    }

    /**@test */
    public function test_a_category_can_be_viewed_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $route = route('admin.categories.show', $category->id);
        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($route)->assertViewIs('admin.category.show');
    }

    /**@test */
    public function test_a_category_can_be_edited_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $route = route('admin.categories.edit', $category->id);
        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($route)->assertViewIs('admin.category.edit');
    }

    /**@test */
    public function test_a_category_can_be_updated_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $data = ['title' => 'xcvxc', 'title_rus' => 'xcvxcv'];
        $route = route('admin.categories.update', $category->id);
        $this->patch($route, $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch($route, $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->patch($route, $data);
        $this->assertTrue(Category::query()->where('title', $data['title'])->exists());
    }

    /**@test */
    public function test_a_category_can_be_deleted_with_premissions(): void
    {
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        /** @var User $user */
        $user = User::factory()->has(Product::factory())->create();
        $route = route('admin.categories.destroy', $category->id);

        $this->delete($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->delete($route)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->delete($route)->assertRedirectToRoute('admin.categories.index');
        $this->assertModelMissing($category)
            ->assertFalse($category->products()->exists());
    }
}
