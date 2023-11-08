<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class HomeTest extends TestCase
{

    /**@test */
    public function test_base_url(): void
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = User::factory()->create();

        $this->get(route('home'))->assertRedirectToRoute('login');

        for ($i = 1; $i <= 3; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('home'))->assertRedirectToRoute($i == User::ROLE_CLIENT ? 'client.products.index' : 'admin.index');
            session()->flush();
        }
    }
}
