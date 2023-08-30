<?php

namespace Tests\Feature\Admin;

use App\Models\Option;
use App\Models\OptionValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_a_option_can_be_viewed_any_with_premissions()
    {
        Option::create(['title' => 'sadfsdf']);
        $user = User::factory()->create();

        $this->get(route('admin.options.index'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.options.index'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.options.index'))->assertViewIs('admin.option.index');
    }

    /**@test */
    public function test_a_option_can_be_created_with_premissions()
    {
        $user = User::factory()->create();

        $this->get(route('admin.options.create'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.options.create'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.options.create'))->assertViewIs('admin.option.create');
    }

    /**@test */
    public function test_a_option_can_be_stored_with_premissions()
    {
        $user = User::factory()->create();
        $data = ['title' => 'asfas', 'optionValues' => ['1', '2', '3']];

        $this->post(route('admin.options.store'), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post(route('admin.options.store'), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->post(route('admin.options.store'), $data);
        $this->assertDatabaseCount('options', 1);
        $this->assertDatabaseCount('optionValues', 3);
        $this->assertEquals($data['title'], Option::first()->title);
    }

    /**@test */
    public function test_a_option_can_be_viewed_with_premissions()
    {
        $option = Option::create(['title' => 'sadfsdf']);
        $user = User::factory()->create();

        $this->get(route('admin.options.show', $option->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.options.show', $option->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.options.show', $option->id))->assertViewIs('admin.option.show');
    }

    /**@test */
    public function test_a_option_can_be_edited_with_premissions()
    {
        $option = Option::create(['title' => 'sadfsdf']);
        $user = User::factory()->create();

        $this->get(route('admin.options.edit', $option->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.options.edit', $option->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.options.edit', $option->id))->assertViewIs('admin.option.edit');
    }

    /**@test */
    public function test_a_option_can_be_updated_with_premissions()
    {
        $user = User::factory()->create();
        $option = Option::create(['title' => 'sadfsdf']);
        OptionValue::factory(4)->create();
        $data = ['title' => 'asfas', 'optionValues' => ['1', '2', '3']];

        $this->patch(route('admin.options.update', $option->id), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch(route('admin.options.update', $option->id), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->patch(route('admin.options.update', $option->id), $data);

        $option = Option::first();
        $this->assertEquals($data['title'], $option->title);
        $optionValues = $option->optionValues()->pluck('value')->all();
        $data['optionValues'] = array_column($data['optionValues'], 'value');
        $this->assertEquals(sort($optionValues), sort($data['optionValues']));
    }

    /**@test */
    public function test_a_option_can_be_deleted_with_premissions()
    {
        $user = User::factory()->create();
        $option = Option::create(['title' => 'sadfsdf']);
        OptionValue::factory()->create();

        $this->delete(route('admin.options.destroy', $option->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->delete(route('admin.options.destroy', $option->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->delete(route('admin.options.destroy', $option->id))->assertRedirect(route('admin.options.index'));
        $this->assertDatabaseCount('options', 0);
        $this->assertTrue($option->optionValues()->count() == 0);
    }
}
