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

        Cache::forever('categories', Category::select('id', 'title', 'title_rus')->get()->all());
    }
}
