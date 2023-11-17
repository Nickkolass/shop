<?php

namespace Database\Seeders;

use Database\Seeders\Services\SeederCompletionService;
use Database\Seeders\Services\SeederFactoryService;
use Database\Seeders\Services\SeederStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    public function __construct(
        private readonly SeederStorageService    $storageService,
        private readonly SeederFactoryService    $factoryService,
        private readonly SeederCompletionService $completionService,
    )
    {
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        DB::beginTransaction();
        $this->storageService->storagePreparation();
        $this->factoryService->factory();
        $this->completionService->completionProducts();
        if (!app()->environment('testing')) {
            $this->completionService->completionUsers();
            $this->storageService->caching();
        }
        DB::commit();
    }
}
