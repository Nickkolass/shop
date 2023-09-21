<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    /**@test */
    public function test_a_category_can_be_viewed_any_with_premissions()
    {
        Category::create(['title' => 'sadfsdf']);
        $user = User::factory()->create();

        $this->get(route('admin.categories.index'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.categories.index'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.categories.index'))->assertViewIs('admin.category.index');
    }

    /**@test */
    public function test_a_category_can_be_created_with_premissions()
    {
        Category::create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $user = User::factory()->create();

        $this->get(route('admin.categories.create'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.categories.create'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.categories.create'))->assertViewIs('admin.category.create');
    }

    /**@test */
    public function test_a_category_can_be_stored_with_premissions()
    {
        $user = User::factory()->create();
        $data = ['title' => 'xcvxc', 'title_rus' => 'cvxcv'];

        $this->post(route('admin.categories.store'), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post(route('admin.categories.store'), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->post(route('admin.categories.store'), $data);
        $this->assertDatabaseCount('categories', 1);

        $category = Category::first();
        $this->assertEquals($data['title'], $category->title);
        $this->assertEquals($data['title_rus'], $category->title_rus);
    }

    /**@test */
    public function test_a_category_can_be_viewed_with_premissions()
    {
        $category = Category::create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $user = User::factory()->create();

        $this->get(route('admin.categories.show', $category->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.categories.show', $category->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.categories.show', $category->id))->assertViewIs('admin.category.show');
    }

    /**@test */
    public function test_a_category_can_be_edited_with_premissions()
    {
        $category = Category::create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $user = User::factory()->create();

        $this->get(route('admin.categories.edit', $category->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.categories.edit', $category->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.categories.edit', $category->id))->assertViewIs('admin.category.edit');
    }

    /**@test */
    public function test_a_category_can_be_updated_with_premissions()
    {
        $user = User::factory()->create();
        $category = Category::create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $data = ['title' => 'xcvxc', 'title_rus' => 'xcvxcv'];

        $this->patch(route('admin.categories.update', $category->id), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch(route('admin.categories.update', $category->id), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->patch(route('admin.categories.update', $category->id), $data);

        $category = Category::first();
        $this->assertEquals($data['title'], $category->title);
        $this->assertEquals($data['title_rus'], $category->title_rus);
    }

    /**@test */
    public function test_a_category_can_be_deleted_with_premissions()
    {
        $category = Category::create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $user = User::factory()->has(Product::factory())->create();

        $this->delete(route('admin.categories.destroy', $category->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->delete(route('admin.categories.destroy', $category->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->delete(route('admin.categories.destroy', $category->id))->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseCount('categories', 0);
        $this->assertTrue($category->products()->count() == 0);
    }
}
