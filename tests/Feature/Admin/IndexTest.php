<?php

namespace Admin;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IndexTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function tearDown(): void
    {
        foreach (Storage::directories() as $dir) if ($dir != 'factory') Storage::deleteDirectory($dir);
        parent::tearDown();
    }

    /**@test */
    public function test_admin_index_url(): void
    {
        $user = User::query()->first();

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
