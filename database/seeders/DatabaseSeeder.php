<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\Property;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;
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
            OptionValue::factory(random_int(3, 4))->create();
        }

        $tags = Tag::factory(10)->create();

        Storage::deleteDirectory('/public/preview_images/');
        Storage::deleteDirectory('/public/product_images/');
        $filesPath = Storage::files('/public/factories/');
        foreach ($filesPath as $filePath) {
            Storage::copy($filePath, str_replace('ories', '', $filePath));
        }

        foreach ($categories as $category) {
            $properties = Property::with(['propertyValues' => function ($q) {
                $q->select('id', 'property_id')->inRandomOrder();
            }])->inRandomOrder()->take(5)->get('id');

            $options = Option::with(['optionValues' => function ($q) {
                $q->select('id', 'option_id')->inRandomOrder();
            }])->inRandomOrder()->take(4)->get('id');

            Category::create($category)->properties()->attach($properties);
            User::factory(1)->create();
            for ($j = 1; $j <= 5; $j++) {
                $ov = $options->random(2)->pluck('optionValues');
                $ov = $ov->pluck(0)->merge($ov->pluck(1));
                $pv = $properties->pluck('propertyValues');
                foreach ($pv as $k => $i) {
                    $pv[$k] = $i->shuffle();
                }
                Product::factory(1)
                    ->has(ProductType::factory(4)
                        ->has(ProductImage::factory(3)))
                    ->hasAttached($tags->random(random_int(3, 5)))
                    ->hasAttached($ov)
                    ->hasAttached($pv->pluck(0))
                    ->create();
            }
        }
        Storage::deleteDirectory('/public/fact/');

        $products = Product::with('productTypes.productImages:productType_id,file_path')->get('id');
        foreach ($products as $product) {
            $optionValues = $product->optionValues()->select('optionValues.id', 'option_id')->get()->groupBy('option_id')->map(function ($o) {
                return $o->pluck('id');
            });
            $optionValues = $optionValues->pop()->crossJoin(...$optionValues);

            foreach ($product->productTypes as $key => $productType) {
                $productImage = $productType->productImages['0']->file_path;
                $previewImage = str_replace('product_images/' . $product->id, 'preview_images', $productImage);
                Storage::copy('public/' . $productImage, 'public/' . $previewImage);
                $productType->count == 0 ? $is_published = 0 : $is_published = 1;
                $productType->update(['is_published' => $is_published, 'preview_image' => $previewImage]);
                $productType->optionValues()->attach($optionValues[$key]);
            }
        }
    }
}
