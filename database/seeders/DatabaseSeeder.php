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
        DB::beginTransaction();
        try {
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

            foreach ($categories as $category) {
                Category::create($category);
            }
            Group::factory(random_int(1, 10))->create();
            User::factory(10)->create();
            $colors = Color::factory(random_int(5, 10))->create();
            $tags = Tag::factory(random_int(5, 10))->create();
            
            $filesPath = Storage::files('/public/factories/');
            foreach($filesPath as $filePath){
                Storage::copy($filePath, str_replace('ories', '', $filePath));
            }
    
            $products = Product::factory(100)
                ->has(ProductImage::factory(3))
                ->create();
                      
            Storage::deleteDirectory('/public/fact/');

            foreach ($products as $product) {
                $tagsIds = $tags->random(random_int(1, count($tags)))->pluck('id');
                $colorsIds = $colors->random(random_int(1, count($colors)))->pluck('id');
                $product->tags()->attach($tagsIds);
                $product->colors()->attach($colorsIds);
                
                $productImage = ProductImage::where('product_id', $product->id)->first('file_path')->toArray();
                $preview_image = str_replace('images', 'preview_images', $productImage['file_path']);
                Storage::copy('public/'.$productImage['file_path'], 'public/'.$preview_image);
                $product->update(['preview_image' => $preview_image]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
