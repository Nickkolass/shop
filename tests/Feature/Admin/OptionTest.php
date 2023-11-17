<?php

namespace Tests\Feature\Admin;

use App\Models\Option;
use App\Models\OptionValue;
use App\Models\User;
use Tests\Feature\Trait\PrepareForTestTrait;
use Tests\TestCase;

class OptionTest extends TestCase
{

    use PrepareForTestTrait;

    /**@test */
    public function test_a_option_can_be_viewed_any_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Option::factory()->create();
        $route = route('admin.options.index');

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.option.index');
    }

    /**@test */
    public function test_a_option_can_be_created_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('admin.options.create');

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.option.create');
    }

    /**@test */
    public function test_a_option_can_be_stored_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $data = ['title' => 'asfas', 'optionValues' => ['1', '2', '3']];
        $route = route('admin.options.store');

        $this->post($route, $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->post($route, $data)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->post($route, $data);
        $this->assertModelExists($option = Option::query()->with('optionValues')->firstWhere('title', $data['title']))
            ->assertEmpty($option->optionValues->pluck('value')->diff($data['optionValues']));
    }

    /**@test */
    public function test_a_option_can_be_viewed_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Option $option */
        $option = Option::factory()->create();
        $route = route('admin.options.show', $option->id);

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.option.show');
    }

    /**@test */
    public function test_a_option_can_be_edited_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Option $option */
        $option = Option::factory()->create();
        $route = route('admin.options.edit', $option->id);
        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.option.edit');
    }

    /**@test */
    public function test_a_option_can_be_updated_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Option $option */
        $option = Option::factory()->create();
        OptionValue::factory(4)->create();
        $data = ['title' => 'asfas', 'optionValues' => ['1', '2', '3']];
        $route = route('admin.options.update', $option->id);

        $this->patch($route, $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->patch($route, $data)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)
            ->patch($route, $data);
        $this->assertModelExists($option = Option::query()->with('optionValues')->firstWhere('title', $data['title']))
            ->assertEmpty($option->optionValues->pluck('value')->diff($data['optionValues']));
    }

    /**@test */
    public function test_a_option_can_be_deleted_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Option $option */
        $option = Option::factory()->create();
        OptionValue::factory()->create();
        $route = route('admin.options.destroy', $option->id);

        $this->delete($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->delete($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)
            ->delete($route)
            ->assertRedirectToRoute('admin.options.index');
        $this->assertModelMissing($option)
            ->assertFalse($option->optionValues()->exists());
    }
}
