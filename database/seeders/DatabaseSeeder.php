<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Color;
use App\Models\Group;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */


    public function run()
    {
            $categories = [
                ['title' => 'chokolate',
                'title_rus' => 'Шоколад ручной работы'],
                ['title' => 'candle',
                'title_rus' => 'Свечи ручной работы'],
                ['title' => 'soap',
                'title_rus' => 'Мыло ручной работы'],
                ['title' => 'aroma',
                'title_rus' => 'Ароматы для автомобиля'],
                ['title' => 'aromahome',
                'title_rus' => 'Ароматы для дома']];

            $colors = Color::factory(10)->create();
            $tags = Tag::factory(10)->create();
            
            $filesPath = Storage::files('/public/factories/');
            foreach($filesPath as $filePath){
                Storage::copy($filePath, str_replace('ories', '', $filePath));
            }
    
            foreach($categories as $category){
            Category::create($category);
            User::factory(2)
            ->has(Group::factory(2)
                ->has(Product::factory(5)
                    ->has(ProductImage::factory(3))
                    ->hasAttached($colors->random(5))
                    ->hasAttached($tags->random(5))))
            ->create();
            }

            Storage::deleteDirectory('/public/fact/');
            
            $products = Product::with('productImages:product_id,file_path')->get();
            $category_id = 20;
            foreach ($products as $product) {
                $productImage = $product->productImages['0']->file_path;
                $previewImage = str_replace('product_images', 'preview_images', $productImage);
                Storage::copy('public/'.$productImage, 'public/'.$previewImage);
                $product->update(['preview_image' => $previewImage, 'category_id' => intdiv($category_id, 20)]);
                $category_id++;
            }
 
     }
 }
