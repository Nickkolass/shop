<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Property;
use App\Models\PropertyValue;
use App\Models\User;
use Tests\TestCase;

class PropertyTest extends TestCase
{

    /**@test */
    public function test_a_property_can_be_viewed_any_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('admin.properties.index');
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
        $this->actingAs($user)->get($route)->assertViewIs('admin.property.index');
    }

    /**@test */
    public function test_a_property_can_be_created_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $route = route('admin.properties.create');
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
        $this->actingAs($user)->get($route)->assertViewIs('admin.property.create');
    }

    /**@test */
    public function test_a_property_can_be_stored_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $data = ['title' => 'asfas', 'category_ids' => [$category->id], 'propertyValues' => ['1', '2', '3']];
        $route = route('admin.properties.store');

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
        $this->assertModelExists($property = Property::query()->with('propertyValues')->firstWhere('title', $data['title']))
            ->assertTrue($property->propertyValues->pluck('value')->diff($data['propertyValues'])->isEmpty());
    }

    /**@test */
    public function test_a_property_can_be_viewed_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $property->categories()->attach($category);
        $route = route('admin.properties.show', $property->id);

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
        $this->actingAs($user)->get($route)->assertViewIs('admin.property.show');
    }

    /**@test */
    public function test_a_property_can_be_edited_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $property->categories()->attach($category);
        $route = route('admin.properties.edit', $property->id);
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
        $this->actingAs($user)->get($route)->assertViewIs('admin.property.edit');
    }

    /**@test */
    public function test_a_property_can_be_updated_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $property->categories()->attach($category);
        PropertyValue::factory(4)->create();
        $data = ['title' => 'asfas', 'category_ids' => [$category->id], 'propertyValues' => ['1', '2', '3']];
        $route = route('admin.properties.update', $property->id);
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
        $this->assertModelExists($property = Property::query()->with('propertyValues')->firstWhere('title', $data['title']))
            ->assertTrue($property->propertyValues->pluck('value')->diff($data['propertyValues'])->isEmpty());
    }

    /**@test */
    public function test_a_property_can_be_deleted_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $property->categories()->attach($category);
        PropertyValue::factory()->create();
        $route = route('admin.properties.destroy', $property->id);
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
        $this->actingAs($user)->delete($route)->assertRedirectToRoute('admin.properties.index');
        $this->assertModelMissing($property)
            ->assertFalse($property->propertyValues()->exists());
    }
}
