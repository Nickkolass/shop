<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\Trait\PrepareForTestTrait;
use Tests\TestCase;

class TagTest extends TestCase
{

    use PrepareForTestTrait;

    /**@test */
    public function test_a_tag_can_be_viewed_any_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Tag::factory()->create();
        $route = route('admin.tags.index');

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.tag.index');
    }

    /**@test */
    public function test_a_tag_can_be_created_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('admin.tags.create');

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.tag.create');
    }

    /**@test */
    public function test_a_tag_can_be_stored_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $data = Tag::factory()->raw();
        $route = route('admin.tags.store');

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
        $this->assertTrue(Tag::query()->where('title', $data['title'])->exists());
    }

    /**@test */
    public function test_a_tag_can_be_viewed_with_premissions(): void
    {
        /** @var Tag $tag */
        $tag = Tag::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('admin.tags.show', $tag->id);

        $this->get($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.tag.show');
    }

    /**@test */
    public function test_a_tag_can_be_edited_with_premissions(): void
    {
        /** @var Tag $tag */
        $tag = Tag::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        $route = route('admin.tags.edit', $tag->id);

        $this->get(route('admin.tags.edit', $tag->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($route)->assertViewIs('admin.tag.edit');
    }

    /**@test */
    public function test_a_tag_can_be_updated_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tag $tag */
        $tag = Tag::factory()->create();
        $data = Tag::factory()->raw();
        $route = route('admin.tags.update', $tag->id);

        $this->patch($route, $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->patch($route, $data)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->patch($route, $data);
        $this->assertEquals($data['title'], Tag::query()->first()->title);
    }

    /**@test */
    public function test_a_tag_can_be_deleted_with_premissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tag $tag */
        $tag = Tag::factory()->create();
        Category::factory()->create();
        /** @phpstan-ignore-next-line */
        Product::factory()->create()->tags()->attach($tag);
        $route = route('admin.tags.destroy', $tag->id);

        $this->delete($route)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->delete($route)->assertNotFound();
        }

        $this->withoutExceptionHandling();

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->delete($route)->assertRedirectToRoute('admin.tags.index');

        $this->assertModelMissing($tag)
            ->assertFalse($tag->products()->exists());
    }
}
