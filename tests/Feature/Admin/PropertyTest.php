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
        $user = User::factory()->create();
        /** @var User $user */

        $this->get(route('admin.properties.index'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.properties.index'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.properties.index'))->assertViewIs('admin.property.index');
    }

    /**@test */
    public function test_a_property_can_be_created_with_premissions(): void
    {
        Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $user = User::factory()->create();
        /** @var User $user */

        $this->get(route('admin.properties.create'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.properties.create'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.properties.create'))->assertViewIs('admin.property.create');
    }

    /**@test */
    public function test_a_property_can_be_stored_with_premissions(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $data = ['title' => 'asfas', 'category_ids' => [$category->id], 'propertyValues' => ['1', '2', '3']];

        $this->post(route('admin.properties.store'), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post(route('admin.properties.store'), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->post(route('admin.properties.store'), $data);
        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseCount('property_values', 3);
        $this->assertEquals($data['title'], Property::query()->first()->title);
    }

    /**@test */
    public function test_a_property_can_be_viewed_with_premissions(): void
    {
        Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $user = User::factory()->create();
        /** @var User $user */

        $this->get(route('admin.properties.show', $property->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.properties.show', $property->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.properties.show', $property->id))->assertViewIs('admin.property.show');
    }

    /**@test */
    public function test_a_property_can_be_edited_with_premissions(): void
    {
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $property->categories()->attach($category);
        $user = User::factory()->create();
        /** @var User $user */

        $this->get(route('admin.properties.edit', $property->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.properties.edit', $property->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.properties.edit', $property->id))->assertViewIs('admin.property.edit');
    }

    /**@test */
    public function test_a_property_can_be_updated_with_premissions(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $property->categories()->attach($category);
        PropertyValue::factory(4)->create();
        $data = ['title' => 'asfas', 'category_ids' => [$category->id], 'propertyValues' => ['1', '2', '3']];

        $this->patch(route('admin.properties.update', $property->id), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch(route('admin.properties.update', $property->id), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->patch(route('admin.properties.update', $property->id), $data);

        $property = Property::query()->first();
        $this->assertEquals($data['title'], $property->title);
        $propertyValues = $property->propertyValues()->pluck('value')->all();
        $data['propertyValues'] = array_column($data['propertyValues'], 'value');
        $this->assertEquals(sort($propertyValues), sort($data['propertyValues']));
    }

    /**@test */
    public function test_a_property_can_be_deleted_with_premissions(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        $category = Category::query()->create(['title' => 'ads', 'title_rus' => 'dgsog']);
        $property = Property::query()->create(['title' => 'sadfsdf']);
        $property->categories()->attach($category);
        PropertyValue::factory()->create();

        $this->delete(route('admin.properties.destroy', $property->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->delete(route('admin.properties.destroy', $property->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->delete(route('admin.properties.destroy', $property->id))->assertRedirect(route('admin.properties.index'));
        $this->assertDatabaseCount('properties', 0);
        $this->assertTrue($property->propertyValues()->count() == 0);
        $this->assertTrue($category->properties()->count() == 0);
    }
}
