<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_base_url()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $this->get(route('home'))->assertRedirect(route('login'));

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('home'))->assertRedirect(route('api.index'));
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->get(route('home'))->assertRedirect(route('admin.index'));
        session()->flush();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('home'))->assertRedirect(route('admin.index'));
    }

    /**@test */
    public function test_admin_index_url()
    {
        $this->seed();
        $user = User::first();

        $this->get(route('home'))->assertRedirect(route('login'));

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.index'))->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.index'))->assertViewIs('admin.index');
            session()->flush();
        }
    }
}
