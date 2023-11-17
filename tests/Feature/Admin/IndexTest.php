<?php

namespace Admin;

use App\Models\User;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_admin_index_url(): void
    {
        $user = User::query()->first();

        $this->get(route('admin.index'))->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get(route('admin.index'))->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get(route('admin.index'))->assertViewIs('admin.index');
        }
    }
}
