<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Color;
use App\Models\Group;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Products\Wick;
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

            // $wicks = ['title' => 'деревянный',
            //         'title' => 'хлопковый'];
                    
            Wick::create($wicks);
            Color::factory(10)->create();
            $tags = Tag::factory(10)->create();
            
            Storage::deleteDirectory('/public/preview_images/');
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
                    ->hasAttached($tags->random(5))))
            ->create();
            }

            Storage::deleteDirectory('/public/fact/');
            
            $products = Product::with('productImages:product_id,file_path')->get();
            $category_id = 1;

            foreach ($products as $product) {
                $group_color_ids = $product->group()->first()->products()->pluck('color_id')->toArray();
                $color_id = $product->color_id;

                if(array_count_values($group_color_ids)[$color_id] != 1){
                    a:
                    $color_id = random_int(1,Color::count());
                    if(!empty($group_color_ids[$color_id])){
                        goto a;
                    }
                }
                $productImage = $product->productImages['0']->file_path;
                $previewImage = str_replace('product_images', 'preview_images', $productImage);
                Storage::copy('public/'.$productImage, 'public/'.$previewImage);
                $product->count == 0 ? $is_published = 0 : $is_published = 1;
                $product->update(['color_id' => $color_id, 'preview_image' => $previewImage, 'category_id' => $category_id, 'is_published' => $is_published]);
                $product->id%20 == 0 ? $category_id++ : '';
            }
 
     }
 }
