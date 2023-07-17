<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\Components\SeederFactoryService;
use Database\Seeders\Components\SeederProductService;
use Database\Seeders\Components\SeederStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

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
    public function run()
    {
        $this->storageService->storagePreparation();
        $this->factoryService->factory();
        $this->productService->completionsOfProducts();

        User::where('id', 1)->update(['role' => 1, 'email' => '1@mail.ru', 'password' => '$2y$10$zEo/vVO3vfXIzHrTdDS1zesl3di.9XddQqXLSuJi1UJf9nVszUvzq']);
        Cache::forever('categories', Category::select('id', 'title', 'title_rus')->get()->toArray());
    }
}
