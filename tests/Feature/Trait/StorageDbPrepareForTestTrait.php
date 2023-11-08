<?php

namespace Tests\Feature\Trait;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

trait StorageDbPrepareForTestTrait
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        View::share('categories', Category::all()->toArray());
    }

    protected function tearDown(): void
    {
        foreach (Storage::directories() as $dir) if ($dir != 'factory') Storage::deleteDirectory($dir);
        parent::tearDown();
    }
}
