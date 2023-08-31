<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
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
        $this->actingAs($user)->get(route('home'))->assertRedirect(route('client.products.index'));
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->get(route('home'))->assertRedirect(route('admin.index'));
        session()->flush();

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('home'))->assertRedirect(route('admin.index'));
    }
}
