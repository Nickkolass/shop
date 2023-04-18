<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Group;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Property;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
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
            ['title' => 'chokolate', 'title_rus' => 'Шоколад ручной работы'],
            ['title' => 'candle', 'title_rus' => 'Свечи ручной работы'],
            ['title' => 'soap', 'title_rus' => 'Мыло ручной работы'],
            ['title' => 'aroma', 'title_rus' => 'Ароматы для автомобиля'],
            ['title' => 'aromahome', 'title_rus' => 'Ароматы для дома'],
        ];
        $options = [
            ['title' => 'Цвет'],
            ['title' => 'Объем'],
            ['title' => 'Аромат'],
            ['title' => 'Размер'],
            ['title' => 'Материал'],
            ['title' => 'Упаковка'],
            ['title' => 'Габариты'],

        ];

        // ['title' => 'Вес'],
        // ['title' => 'Вес'],

        // ['title' => 'is_published'],
        // ['title' => 'count'],
        // ['title' => 'Цена'],

        for ($k = 1; $k <= 10; $k++) {
            Property::factory(1)
                ->has(PropertyValue::factory(random_int(2, 4)))
                ->create();
        }

        foreach ($options as $option) {
            Option::create($option);
            OptionValue::factory(random_int(2, 4))->create();
        }

        // PropertyValue::factory(12)->create();
        $tags = Tag::factory(10)->create();

        Storage::deleteDirectory('/public/preview_images/');
        $filesPath = Storage::files('/public/factories/');
        foreach ($filesPath as $filePath) {
            Storage::copy($filePath, str_replace('ories', '', $filePath));
        }

        foreach ($categories as $category) {
            $properties = Property::inRandomOrder()->take(5)->get('id');
            $options = Option::inRandomOrder()->take(4)->get('id');
            Category::create($category)->properties()->attach($properties);
            for ($i = 1; $i <= 2; $i++) {
                User::factory(1)->create();
                for ($j = 1; $j <= 2; $j++) {
                    $opt = $options->random(2);
                    Group::factory(1)
                        ->has(Product::factory(5)
                            ->has(ProductImage::factory(3))
                            ->hasAttached($tags->random(random_int(3, 5)))
                            ->hasAttached($opt['0']->optionValues()->take(random_int(2, $opt['0']->optionValues()->count()))->get('id'))
                            ->hasAttached($opt['1']->optionValues()->take(random_int(2, $opt['1']->optionValues()->count()))->get('id'))
                            ->hasAttached($properties['0']->propertyValues()->inRandomOrder()->take(1)->get('id'))
                            ->hasAttached($properties['1']->propertyValues()->inRandomOrder()->take(1)->get('id'))
                            ->hasAttached($properties['2']->propertyValues()->inRandomOrder()->take(1)->get('id'))
                            ->hasAttached($properties['3']->propertyValues()->inRandomOrder()->take(1)->get('id'))
                            ->hasAttached($properties['4']->propertyValues()->inRandomOrder()->take(1)->get('id')))
                        ->create();
                    // ->hasAttached($category->properties()->inRandomOrder()->take(2)->get(), new Sequence(
                    //     fn () => ['value' => fake()->word()],
                    // ))
                }
            }
        }
        Storage::deleteDirectory('/public/fact/');

        $products = Product::select('id', 'count')->with('productImages:product_id,file_path')->get();
        foreach ($products as $product) {
            $productImage = $product->productImages['0']->file_path;
            $previewImage = str_replace('product_images', 'preview_images', $productImage);
            Storage::copy('public/' . $productImage, 'public/' . $previewImage);
            $product->count == 0 ? $is_published = 0 : $is_published = 1;
            $product->update(['is_published' => $is_published, 'preview_image' => $previewImage]);
        }
    }
}
