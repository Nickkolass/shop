<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Components\SeederFactoryService;
use Database\Seeders\Components\SeederProductService;
use Database\Seeders\Components\SeederStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function __construct(
        private readonly SeederStorageService $storageService,
        private readonly SeederFactoryService $factoryService,
        private readonly SeederProductService $productService,
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
        $this->storageService->storagePreparation();
        $this->factoryService->factory();
        $this->productService->completionsOfProducts();
        $this->storageService->caching();

        $users = User::query()
            ->take(3)
            ->get('id')
            ->map(function (User $user, int $i) {
                $i++;
                return [
                    'id' => $user->id,
                    'email' => $i . '@mail.ru',
                    'password' => Hash::make((string)$i),
                    'role' => $i,
                ];
            })
            ->toArray();
        User::query()->upsert($users, 'id');
    }
}
