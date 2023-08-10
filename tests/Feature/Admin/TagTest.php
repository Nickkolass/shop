<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_a_tag_can_be_viewed_any_with_premissions()
    {
        $tag = Tag::factory()->create();
        $user = User::factory()->create();

        $this->get(route('admin.tags.index'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $this->actingAs($user)->get(route('admin.tags.index'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.tags.index'))->assertViewIs('admin.tag.index');
    }

    /**@test */
    public function test_a_tag_can_be_created_with_premissions()
    {
        $user = User::factory()->create();

        $this->get(route('admin.tags.create'))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.tags.create'))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.tags.create'))->assertViewIs('admin.tag.create');
    }

    /**@test */
    public function test_a_tag_can_be_stored_with_premissions()
    {
        $user = User::factory()->create();
        $data = Tag::factory()->raw();

        $this->post(route('admin.tags.store'), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post(route('admin.tags.store'), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->post(route('admin.tags.store'), $data);
        $this->assertDatabaseCount('tags', 1);

        $tag = Tag::first();
        $this->assertEquals($data['title'], $tag['title']);
    }

    /**@test */
    public function test_a_tag_can_be_viewed_with_premissions()
    {
        $tag = Tag::factory()->create();
        $user = User::factory()->create();

        $this->get(route('admin.tags.show', $tag->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.tags.show', $tag->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.tags.show', $tag->id))->assertViewIs('admin.tag.show');
    }

    /**@test */
    public function test_a_tag_can_be_edited_with_premissions()
    {
        $tag = Tag::factory()->create();
        $user = User::factory()->create();

        $this->get(route('admin.tags.edit', $tag->id))->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.tags.edit', $tag->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.tags.edit', $tag->id))->assertViewIs('admin.tag.edit');
    }

    /**@test */
    public function test_a_tag_can_be_updated_with_premissions()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $data = Tag::factory()->raw();

        $this->patch(route('admin.tags.update', $tag->id), $data)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch(route('admin.tags.update', $tag->id), $data)->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->patch(route('admin.tags.update', $tag->id), $data);
        $this->assertEquals($data['title'], Tag::first()->title);
    }

    /**@test */
    public function test_a_tag_can_be_deleted_with_premissions()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        Category::create(['title' => 'assdg', 'title_rus' => 'asdasd']);
        Product::factory()->create()->tags()->attach($tag);

        $this->delete('/admin/tags/' . $tag->id)->assertNotFound();

        for ($i = 2; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->delete(route('admin.tags.destroy', $tag->id))->assertNotFound();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->delete(route('admin.tags.destroy', $tag->id))->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseCount('tags', 0);
        $this->assertTrue($tag->products()->count() == 0);
    }
}
