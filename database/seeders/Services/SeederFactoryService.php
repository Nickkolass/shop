<?php

namespace Database\Seeders\Services;

use App\Models\Category;
use App\Models\CommentImage;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\Property;
use App\Models\PropertyValue;
use App\Models\RatingAndComment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SeederFactoryService
{
    public function factory(): void
    {
        $tags = Tag::factory(10)->create();
        $properties = Property::factory(10)
            ->has(PropertyValue::factory(3))
            ->create()
            ->load('propertyValues:id,property_id');

        $options = Option::factory(7)
            ->has(OptionValue::factory(4))
            ->create()
            ->load('optionValues:id,option_id');

        $category_count = app()->environment('testing') ? 3 : 5;
        $categories = Category::factory($category_count)
            /** @phpstan-ignore-next-line */
            ->sequence(...collect(Category::$titles)->transform(fn(string $title) => ['title' => $title]))
            ->create();
        User::factory($category_count)->create();

        /** @var Category $category */
        foreach ($categories as $category) {
            $category_properties = $properties->random(5);
            $category->properties()->attach($category_properties);
            $category_propertyValues = $category_properties->pluck('propertyValues');
            $category_optionValues = $options->random(4)->pluck('optionValues');

            for ($j = 1; $j <= $category_count; $j++) {
                $product_tags = $tags->random(3);
                $product_propertyValues = $category_propertyValues
                    ->random(3)
                    ->transform(fn(Collection $propertyValue) => $propertyValue->random())
                    ->flatten();

                $product_optionValues = $category_optionValues
                    ->random(2)
                    ->transform(fn(Collection $optionValue) => $optionValue->random(2))
                    ->flatten();

                Product::factory()
                    ->has(ProductType::factory(4)
                        ->has(ProductImage::factory(3)))
                    ->has(RatingAndComment::factory()
                        ->has(CommentImage::factory()))
                    ->hasAttached($product_tags)
                    ->hasAttached($product_propertyValues)
                    ->hasAttached($product_optionValues)
                    ->create();
            }
        }

        for ($i = 1; $i <= 5; $i++) {
            Order::factory()->create();
            $count_orderPerformers = count(Cache::get('saler_ids_for_factory_order_performers'));
            for ($n = 0; $n < $count_orderPerformers; $n++) {
                OrderPerformer::factory()->create();
            }
        }
    }
}
