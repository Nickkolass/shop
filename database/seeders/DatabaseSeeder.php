<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\Components\SeederFactoryService;
use Database\Seeders\Components\SeederProductService;
use Database\Seeders\Components\SeederStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private SeederStorageService $storageService;
    private SeederProductService $productService;
    private SeederFactoryService $factoryService;

    public function __construct(SeederStorageService $storageService, SeederFactoryService $factoryService, SeederProductService $productService)
    {
        $this->storageService = $storageService;
        $this->factoryService = $factoryService;
        $this->productService = $productService;
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->storageService->storagePreparation();
        $this->factoryService->factory();
        $this->productService->completionsOfProducts();

        $user_upd = [
            ['id' => 1, 'email' => '1@mail.ru', 'password' => Hash::make(1), 'role' => 1],
            ['id' => 2, 'email' => '2@mail.ru', 'password' => Hash::make(2), 'role' => 2],
            ['id' => 3, 'email' => '3@mail.ru', 'password' => Hash::make(3), 'role' => 3],
        ];
        User::upsert($user_upd, ['id']);
        Cache::forever('categories', Category::select('id', 'title', 'title_rus')->get()->all());
    }
}
