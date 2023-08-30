<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Components\SeederCacheService;
use Database\Seeders\Components\SeederFactoryService;
use Database\Seeders\Components\SeederProductService;
use Database\Seeders\Components\SeederStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private SeederStorageService $storageService;
    private SeederProductService $productService;
    private SeederFactoryService $factoryService;
    private SeederCacheService $cacheService;

    public function __construct(
        SeederStorageService $storageService,
        SeederFactoryService $factoryService,
        SeederProductService $productService,
        SeederCacheService   $cacheService,
    )
    {
        $this->storageService = $storageService;
        $this->factoryService = $factoryService;
        $this->productService = $productService;
        $this->cacheService = $cacheService;
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        cache()->flush();
        $this->storageService->storagePreparation();
        $this->factoryService->factory();
        $this->productService->completionsOfProducts();
        $this->cacheService->caching();

        User::query()
            ->take(3)
            ->get()
            ->map(function (User $user, $i) {
                // $i чтобы не было ошибок при многократном наполнении БД в тестах
                $i++;
                $user->email = $i . '@mail.ru';
                $user->password = Hash::make($i);
                $user->role = $i;
                $user->save();
            });
    }
}
