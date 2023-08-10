<?php

namespace Database\Seeders\Components;

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

class SeederFactoryService
{
    public function factory(): void
    {
        $categories = SeederInitialData::getCategories();
        $options = SeederInitialData::getOptions();

        $tags = Tag::factory(10)->create();
        for ($k = 1; $k <= 10; $k++) {
            Property::factory(1)
                ->has(PropertyValue::factory(random_int(2, 4)))
                ->create();
        }
        foreach ($options as $option) {
            Option::create($option);
            OptionValue::factory(random_int(3, 4))->create();
        }

        foreach ($categories as $category) {
            $properties = Property::query()
                ->with(['propertyValues' => fn($q) => $q->select('id', 'property_id')->inRandomOrder()])
                ->inRandomOrder()
                ->take(5)
                ->get('id');

            $options = Option::query()
                ->with(['optionValues' => fn($q) => $q->select('id', 'option_id')->inRandomOrder()])
                ->inRandomOrder()
                ->take(4)
                ->get('id');

            Category::create($category)->properties()->attach($properties);
            User::factory(1)->create();
            for ($j = 1; $j <= 5; $j++) {
                $propertyValues = $properties->pluck('propertyValues')->shuffle()->pluck(0);
                $optionValues = $options->random(2)->pluck('optionValues')->random(2)
                    ->map(fn ($optionValue) => $optionValue->random(2))->flatten();

                Product::factory(1)
                    ->has(ProductType::factory(4)
                        ->has(ProductImage::factory(3)))
                    ->has(RatingAndComment::factory(1)
                        ->has(CommentImage::factory(3)))
                    ->hasAttached($tags->random(random_int(3, 5)))
                    ->hasAttached($optionValues)
                    ->hasAttached($propertyValues)
                    ->create();
            }
        }
        for ($m = 1; $m <= 10; $m++) {
            Order::factory(1)->create();

            for ($n = 0; $n < count(cache()->get('factoryOrders')); $n++) {
                cache()->put('factoryCurrentOrderSaler', $n, 60);
                OrderPerformer::factory(1)->create();
            }
        }
    }
}
